<?php
/**
 * Mistral AI Provider
 * High-performance AI models with competitive pricing
 */

if (!defined('APP_INIT')) {
    http_response_code(404);
    exit;
}

class MistralAIProvider extends AIProviderBase {
    
    private $api_key;
    private $base_url = 'https://api.mistral.ai/v1';
    
    public function __construct($module) {
        parent::__construct($module);
        $this->api_key = $this->GetApiKey();
    }
    
    /**
     * Get API key from config or preferences
     */
    private function GetApiKey() {
        // Check config.php first
        if (defined('MAS_AI_MISTRAL_KEY')) {
            return constant('MAS_AI_MISTRAL_KEY');
        }
        
        // Check encrypted preferences
        $this->module->LoadClass('ConfigManager');
        $config = new ConfigManager($this->module);
        return $config->GetApiKey('mistral');
    }
    
    /**
     * Validate provider configuration
     */
    public function Validate() {
        return !empty($this->api_key);
    }
    
    /**
     * Generate content using Mistral AI
     */
    public function Generate($prompt, $options = array()) {
        if (!$this->Validate()) {
            return array(
                'success' => false,
                'error' => 'Mistral AI API key not configured'
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
            $model = $options['model'] ?? 'mistral-large-latest';
            $max_tokens = $options['max_tokens'] ?? 1000;
            $temperature = $options['temperature'] ?? 0.7;
            
            $request_data = array(
                'model' => $model,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'max_tokens' => $max_tokens,
                'temperature' => $temperature,
                'stream' => false
            );
            
            $response = $this->MakeRequest($request_data);
            
            if ($response['success']) {
                $content = $response['data']['choices'][0]['message']['content'] ?? '';
                
                // Log the request
                $security->LogRequest(null, 'mistral', 'generation');
                
                return array(
                    'success' => true,
                    'content' => $content,
                    'provider' => 'mistral',
                    'model' => $model,
                    'usage' => $response['data']['usage'] ?? null
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
                'error' => 'Mistral AI error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Make HTTP request to Mistral AI API
     */
    private function MakeRequest($data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->base_url . '/chat/completions',
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
            'mistral-large-latest' => 'Mistral Large (Latest)',
            'mistral-small-latest' => 'Mistral Small (Latest)',
            'mistral-tiny-latest' => 'Mistral Tiny (Latest)',
            'open-mistral-7b' => 'Open Mistral 7B',
            'open-mixtral-8x7b' => 'Open Mixtral 8x7B',
            'open-mixtral-8x22b' => 'Open Mixtral 8x22B'
        );
    }
    
    /**
     * Get provider information
     */
    public function GetInfo() {
        return array(
            'name' => 'Mistral AI',
            'description' => 'High-performance AI models with competitive pricing',
            'models' => $this->GetModels(),
            'configured' => $this->Validate(),
            'pricing' => array(
                'mistral-large-latest' => '$8/1M input, $24/1M output',
                'mistral-small-latest' => '$2/1M input, $6/1M output',
                'mistral-tiny-latest' => '$0.25/1M input, $0.25/1M output'
            )
        );
    }
}
?>
