<?php
/**
 * Cohere AI Provider
 * Enterprise-grade AI with strong multilingual support
 */

if (!defined('APP_INIT')) {
    http_response_code(404);
    exit;
}

class CohereProvider extends AIProviderBase {
    
    private $api_key;
    private $base_url = 'https://api.cohere.ai/v1';
    
    public function __construct($module) {
        parent::__construct($module);
        $this->api_key = $this->GetApiKey();
    }
    
    /**
     * Get API key from config or preferences
     */
    private function GetApiKey() {
        // Check config.php first
        if (defined('MAS_AI_COHERE_KEY')) {
            return constant('MAS_AI_COHERE_KEY');
        }
        
        // Check encrypted preferences
        $this->module->LoadClass('ConfigManager');
        $config = new ConfigManager($this->module);
        return $config->GetApiKey('cohere');
    }
    
    /**
     * Validate provider configuration
     */
    public function Validate() {
        return !empty($this->api_key);
    }
    
    /**
     * Generate content using Cohere AI
     */
    public function Generate($prompt, $options = array()) {
        if (!$this->Validate()) {
            return array(
                'success' => false,
                'error' => 'Cohere AI API key not configured'
            );
        }
        
        $this->module->LoadClass('SecurityHelper');
        $security = new SecurityHelper($this->module);
        
        // Check rate limit
        $rate_check = $security->CheckRateLimit();
        if (!$rate_check['allowed']) {
            return array(
                'success' => false,
                'error' => 'Rate limit exceeded. Please wait before making another request.'
            );
        }
        
        try {
            $model = $options['model'] ?? 'command-r-plus';
            $max_tokens = $options['max_tokens'] ?? 1000;
            $temperature = $options['temperature'] ?? 0.7;
            
            $request_data = array(
                'model' => $model,
                'message' => $prompt,
                'max_tokens' => $max_tokens,
                'temperature' => $temperature,
                'stream' => false,
                'chat_history' => array()
            );
            
            $response = $this->MakeRequest($request_data);
            
            if ($response['success']) {
                $content = $response['data']['text'] ?? '';
                
                // Log the request
                $security->LogRequest(null, 'cohere', 'generation');
                
                return array(
                    'success' => true,
                    'content' => $content,
                    'provider' => 'cohere',
                    'model' => $model,
                    'usage' => $response['data']['meta'] ?? null
                );
            } else {
                return array(
                    'success' => false,
                    'error' => $response['error']
                );
            }
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'error' => 'Cohere AI error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Make HTTP request to Cohere AI API
     */
    private function MakeRequest($data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->base_url . '/chat',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key
            ),
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'MAS-AI-Assistant/1.0'
        ));
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return array(
                'success' => false,
                'error' => 'cURL error: ' . $error
            );
        }
        
        if ($http_code !== 200) {
            return array(
                'success' => false,
                'error' => 'HTTP error ' . $http_code . ': ' . $response
            );
        }
        
        return array(
            'success' => true,
            'data' => json_decode($response, true)
        );
    }
    
    /**
     * Get available models
     */
    public function GetModels() {
        return array(
            'command-r-plus' => 'Command R+ (Latest)',
            'command-r' => 'Command R',
            'command-light' => 'Command Light',
            'command' => 'Command',
            'command-nightly' => 'Command Nightly'
        );
    }
    
    /**
     * Get provider information
     */
    public function GetInfo() {
        return array(
            'name' => 'Cohere AI',
            'description' => 'Enterprise-grade AI with strong multilingual support',
            'models' => $this->GetModels(),
            'configured' => $this->Validate(),
            'features' => array(
                'Multilingual Support',
                'Enterprise Security',
                'RAG Capabilities',
                'Tool Use',
                'Citation Support'
            ),
            'pricing' => array(
                'command-r-plus' => '$3/1M input, $15/1M output',
                'command-r' => '$3/1M input, $15/1M output',
                'command-light' => '$1/1M input, $1/1M output',
                'command' => '$1/1M input, $1/1M output'
            )
        );
    }
}
?>
