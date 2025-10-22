<?php
/**
 * Base AI Provider Class for MAS AI Assistant
 * 
 * Abstract base class that all AI providers extend
 * Provides standard interface for AI interactions
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

abstract class AIProviderBase
{
    protected $module;
    protected $config;
    protected $api_key;
    protected $last_error = '';
    protected $max_retries = 3;
    
    /**
     * Constructor
     */
    public function __construct($module) {
        $this->module = $module;
        $this->LoadConfig();
    }
    
    /**
     * Load provider configuration
     */
    protected function LoadConfig() {
        $this->module->LoadClass('ConfigManager');
        $config_manager = new ConfigManager($this->module);
        
        $provider_name = $this->GetProviderName();
        $this->config = $config_manager->GetProviderConfig($provider_name);
        $this->api_key = $config_manager->GetApiKey($provider_name);
    }
    
    /**
     * Get provider name (must be implemented by child classes)
     */
    abstract protected function GetProviderName();
    
    /**
     * Validate configuration
     */
    public function Validate() {
        // Base validation - check if provider requires API key
        if ($this->RequiresApiKey() && empty($this->api_key)) {
            $this->last_error = 'API key is required for this provider';
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if provider requires API key
     */
    protected function RequiresApiKey() {
        return true; // Most providers require API keys
    }
    
    /**
     * Generate content
     */
    public function Generate($prompt, $options = array()) {
        // Validate before generating
        if (!$this->Validate()) {
            return array(
                'success' => false,
                'error' => $this->last_error
            );
        }
        
        // Sanitize prompt
        $this->module->LoadClass('SecurityHelper');
        $security = new SecurityHelper($this->module);
        $prompt = $security->SanitizePrompt($prompt);
        
        // Attempt generation with retries
        $attempts = 0;
        $last_exception = null;
        
        while ($attempts < $this->max_retries) {
            try {
                $result = $this->DoGenerate($prompt, $options);
                
                if ($result['success']) {
                    return $result;
                }
                
                $attempts++;
                if ($attempts < $this->max_retries) {
                    sleep(1); // Wait before retry
                }
                
            } catch (Exception $e) {
                $last_exception = $e;
                $attempts++;
                
                if ($attempts < $this->max_retries) {
                    sleep(1);
                }
            }
        }
        
        // All retries failed
        return array(
            'success' => false,
            'error' => $last_exception ? $last_exception->getMessage() : 'Generation failed after ' . $this->max_retries . ' attempts'
        );
    }
    
    /**
     * Perform actual generation (implemented by child classes)
     */
    abstract protected function DoGenerate($prompt, $options = array());
    
    /**
     * Make HTTP request to API
     */
    protected function MakeRequest($url, $data, $headers = array()) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL error: ' . $error);
        }
        
        if ($http_code >= 400) {
            throw new Exception('HTTP error ' . $http_code . ': ' . $response);
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Get last error message
     */
    public function GetLastError() {
        return $this->last_error;
    }
    
    /**
     * Build system prompt for content generation
     */
    protected function BuildSystemPrompt($type = 'content') {
        $prompts = array(
            'content' => 'You are an expert content writer. Create high-quality, SEO-optimized content for websites. Write in a clear, engaging style with proper HTML formatting.',
            'design' => 'You are an expert web designer and front-end developer. Create modern, responsive HTML/CSS designs following best practices and accessibility standards.',
            'template' => 'You are an expert CMSMS template developer. Create well-structured Smarty templates with proper CMSMS tags and clean code.',
            'seo' => 'You are an SEO expert. Analyze content and provide optimization recommendations including meta tags, keywords, and structure improvements.'
        );
        
        return isset($prompts[$type]) ? $prompts[$type] : $prompts['content'];
    }
    
    /**
     * Format response for consistent output
     */
    protected function FormatResponse($content, $metadata = array()) {
        return array(
            'success' => true,
            'content' => $content,
            'metadata' => $metadata,
            'provider' => $this->GetProviderName(),
            'timestamp' => time()
        );
    }
}
?>


