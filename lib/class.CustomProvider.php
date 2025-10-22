<?php
/**
 * Custom AI Provider for user-defined APIs
 * Allows users to add their own AI providers with custom endpoints
 */

if (!defined('APP_INIT')) {
    http_response_code(404);
    exit;
}

class CustomProvider extends AIProviderBase {
    
    private $provider_config;
    private $api_key;
    private $endpoint;
    private $model;
    private $headers;
    private $request_format;
    
    public function __construct($module, $provider_name) {
        parent::__construct($module);
        $this->provider_name = $provider_name;
        $this->LoadProviderConfig();
    }
    
    /**
     * Load custom provider configuration
     */
    private function LoadProviderConfig() {
        $config_json = $this->module->GetPreference('custom_provider_' . $this->provider_name, '');
        
        if (empty($config_json)) {
            throw new Exception('Custom provider configuration not found');
        }
        
        $this->provider_config = json_decode($config_json, true);
        
        if (!$this->provider_config) {
            throw new Exception('Invalid custom provider configuration');
        }
        
        $this->api_key = $this->provider_config['api_key'] ?? '';
        $this->endpoint = $this->provider_config['endpoint'] ?? '';
        $this->model = $this->provider_config['model'] ?? '';
        $this->headers = $this->provider_config['headers'] ?? [];
        $this->request_format = $this->provider_config['request_format'] ?? 'openai';
        
        if (empty($this->api_key) || empty($this->endpoint)) {
            throw new Exception('Custom provider missing required configuration');
        }
    }
    
    /**
     * Validate provider configuration
     */
    public function Validate() {
        try {
            $this->LoadProviderConfig();
            return !empty($this->api_key) && !empty($this->endpoint);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Generate content using custom provider
     */
    public function Generate($prompt, $options = array()) {
        if (!$this->Validate()) {
            return array(
                'success' => false,
                'error' => 'Custom provider not properly configured'
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
            $request_data = $this->PrepareRequest($prompt, $options);
            $response = $this->MakeRequest($request_data);
            
            if ($response['success']) {
                $content = $this->ParseResponse($response['data']);
                
                // Log the request
                $security->LogRequest(null, $this->provider_name, 'custom_generation');
                
                return array(
                    'success' => true,
                    'content' => $content,
                    'provider' => $this->provider_name,
                    'model' => $this->model
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
                'error' => 'Custom provider error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Prepare request data based on format
     */
    private function PrepareRequest($prompt, $options) {
        $max_tokens = $options['max_tokens'] ?? 1000;
        $temperature = $options['temperature'] ?? 0.7;
        
        switch ($this->request_format) {
            case 'openai':
                return array(
                    'model' => $this->model,
                    'messages' => array(
                        array(
                            'role' => 'user',
                            'content' => $prompt
                        )
                    ),
                    'max_tokens' => $max_tokens,
                    'temperature' => $temperature
                );
                
            case 'anthropic':
                return array(
                    'model' => $this->model,
                    'max_tokens' => $max_tokens,
                    'temperature' => $temperature,
                    'messages' => array(
                        array(
                            'role' => 'user',
                            'content' => $prompt
                        )
                    )
                );
                
            case 'huggingface':
                return array(
                    'inputs' => $prompt,
                    'parameters' => array(
                        'max_new_tokens' => $max_tokens,
                        'temperature' => $temperature,
                        'return_full_text' => false
                    )
                );
                
            case 'custom':
                // Use custom request format from config
                $custom_format = $this->provider_config['custom_request'] ?? array();
                $custom_format['prompt'] = $prompt;
                $custom_format['max_tokens'] = $max_tokens;
                $custom_format['temperature'] = $temperature;
                return $custom_format;
                
            default:
                throw new Exception('Unsupported request format: ' . $this->request_format);
        }
    }
    
    /**
     * Make HTTP request to custom provider
     */
    private function MakeRequest($data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(
                array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->api_key
                ),
                $this->headers
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
     * Parse response based on format
     */
    private function ParseResponse($data) {
        switch ($this->request_format) {
            case 'openai':
                return $data['choices'][0]['message']['content'] ?? '';
                
            case 'anthropic':
                return $data['content'][0]['text'] ?? '';
                
            case 'huggingface':
                if (isset($data[0]['generated_text'])) {
                    return $data[0]['generated_text'];
                } elseif (isset($data['generated_text'])) {
                    return $data['generated_text'];
                }
                return '';
                
            case 'custom':
                // Use custom response parsing from config
                $response_path = $this->provider_config['response_path'] ?? 'content';
                $path_parts = explode('.', $response_path);
                $content = $data;
                
                foreach ($path_parts as $part) {
                    if (isset($content[$part])) {
                        $content = $content[$part];
                    } else {
                        return '';
                    }
                }
                
                return is_string($content) ? $content : '';
                
            default:
                return '';
        }
    }
    
    /**
     * Get provider information
     */
    public function GetInfo() {
        return array(
            'name' => $this->provider_config['name'] ?? $this->provider_name,
            'description' => $this->provider_config['description'] ?? 'Custom AI Provider',
            'model' => $this->model,
            'endpoint' => $this->endpoint,
            'format' => $this->request_format,
            'configured' => $this->Validate()
        );
    }
    
    /**
     * Test connection to custom provider
     */
    public function TestConnection() {
        try {
            $test_prompt = "Hello, this is a test message. Please respond with 'Test successful'.";
            $result = $this->Generate($test_prompt, array('max_tokens' => 50));
            
            return array(
                'success' => $result['success'],
                'message' => $result['success'] ? 'Connection test successful' : $result['error']
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            );
        }
    }
}
?>
