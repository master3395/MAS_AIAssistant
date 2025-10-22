<?php
/**
 * Perplexity AI Provider
 * Real-time web search and AI-powered responses
 */

if (!defined('APP_INIT')) {
    http_response_code(404);
    exit;
}

class PerplexityProvider extends AIProviderBase {
    
    private $api_key;
    private $base_url = 'https://api.perplexity.ai/chat/completions';
    
    public function __construct($module) {
        parent::__construct($module);
        $this->api_key = $this->GetApiKey();
    }
    
    /**
     * Get API key from config or preferences
     */
    private function GetApiKey() {
        // Check config.php first
        if (defined('MAS_AI_PERPLEXITY_KEY')) {
            return constant('MAS_AI_PERPLEXITY_KEY');
        }
        
        // Check encrypted preferences
        $this->module->LoadClass('ConfigManager');
        $config = new ConfigManager($this->module);
        return $config->GetApiKey('perplexity');
    }
    
    /**
     * Validate provider configuration
     */
    public function Validate() {
        return !empty($this->api_key);
    }
    
    /**
     * Generate content using Perplexity AI
     */
    public function Generate($prompt, $options = array()) {
        if (!$this->Validate()) {
            return array(
                'success' => false,
                'error' => 'Perplexity AI API key not configured'
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
            $model = $options['model'] ?? 'llama-3.1-sonar-small-128k-online';
            $max_tokens = $options['max_tokens'] ?? 1000;
            $temperature = $options['temperature'] ?? 0.2;
            $search_web = $options['search_web'] ?? true;
            
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
            
            // Add web search capability for online models
            if ($search_web && strpos($model, 'online') !== false) {
                $request_data['search_domain_filter'] = array();
                $request_data['search_recency_filter'] = 'month';
            }
            
            $response = $this->MakeRequest($request_data);
            
            if ($response['success']) {
                $content = $response['data']['choices'][0]['message']['content'] ?? '';
                
                // Log the request
                $security->LogRequest(null, 'perplexity', 'generation');
                
                return array(
                    'success' => true,
                    'content' => $content,
                    'provider' => 'perplexity',
                    'model' => $model,
                    'usage' => $response['data']['usage'] ?? null,
                    'citations' => $response['data']['citations'] ?? null
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
                'error' => 'Perplexity AI error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Make HTTP request to Perplexity AI API
     */
    private function MakeRequest($data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->base_url,
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
            'llama-3.1-sonar-small-128k-online' => 'Llama 3.1 Sonar Small (Online)',
            'llama-3.1-sonar-large-128k-online' => 'Llama 3.1 Sonar Large (Online)',
            'llama-3.1-sonar-small-128k-chat' => 'Llama 3.1 Sonar Small (Chat)',
            'llama-3.1-sonar-large-128k-chat' => 'Llama 3.1 Sonar Large (Chat)',
            'llama-3.1-sonar-huge-128k-online' => 'Llama 3.1 Sonar Huge (Online)',
            'llama-3.1-sonar-huge-128k-chat' => 'Llama 3.1 Sonar Huge (Chat)'
        );
    }
    
    /**
     * Get provider information
     */
    public function GetInfo() {
        return array(
            'name' => 'Perplexity AI',
            'description' => 'Real-time web search and AI-powered responses',
            'models' => $this->GetModels(),
            'configured' => $this->Validate(),
            'features' => array(
                'Web Search Integration',
                'Real-time Information',
                'Citation Support',
                'Domain Filtering',
                'Recency Filtering'
            ),
            'pricing' => array(
                'sonar-small' => '$5/1M tokens',
                'sonar-large' => '$5/1M tokens',
                'sonar-huge' => '$5/1M tokens'
            )
        );
    }
}
?>
