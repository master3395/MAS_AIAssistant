<?php
/**
 * Configuration Manager for MAS AI Assistant
 * 
 * Handles API key storage (encrypted preferences + config.php support)
 * Provider configuration and settings management
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

class ConfigManager
{
    private $module;
    private $encryption_method = 'AES-256-CBC';
    
    /**
     * Constructor
     */
    public function __construct($module) {
        $this->module = $module;
    }
    
    /**
     * Get API key for provider (checks config.php first, then encrypted prefs)
     */
    public function GetApiKey($provider) {
        // Check config.php constants first
        $const_name = 'MAS_AI_' . strtoupper($provider) . '_KEY';
        if (defined($const_name)) {
            return constant($const_name);
        }
        
        // Fall back to encrypted preference
        $encrypted = $this->module->GetPreference('api_key_' . $provider, '');
        if (empty($encrypted)) {
            return '';
        }
        
        return $this->Decrypt($encrypted);
    }
    
    /**
     * Set API key for provider (encrypted in database)
     */
    public function SetApiKey($provider, $key) {
        if (empty($key)) {
            $this->module->RemovePreference('api_key_' . $provider);
            return true;
        }
        
        $encrypted = $this->Encrypt($key);
        $this->module->SetPreference('api_key_' . $provider, $encrypted);
        return true;
    }
    
    /**
     * Get encryption key (site-specific)
     */
    private function GetEncryptionKey() {
        $key = $this->module->GetPreference('encryption_key', '');
        
        if (empty($key)) {
            // Generate new encryption key
            $key = bin2hex(random_bytes(32));
            $this->module->SetPreference('encryption_key', $key);
        }
        
        return $key;
    }
    
    /**
     * Get encryption IV (site-specific)
     */
    private function GetEncryptionIV() {
        $iv = $this->module->GetPreference('encryption_iv', '');
        
        if (empty($iv)) {
            // Generate new IV
            $iv_length = openssl_cipher_iv_length($this->encryption_method);
            $iv = bin2hex(random_bytes($iv_length));
            $this->module->SetPreference('encryption_iv', $iv);
        }
        
        return $iv;
    }
    
    /**
     * Encrypt data
     */
    private function Encrypt($data) {
        if (empty($data)) {
            return '';
        }
        
        $key = hex2bin($this->GetEncryptionKey());
        $iv = hex2bin(substr($this->GetEncryptionIV(), 0, 32)); // Get proper IV length
        
        $encrypted = openssl_encrypt(
            $data,
            $this->encryption_method,
            $key,
            0,
            $iv
        );
        
        return base64_encode($encrypted);
    }
    
    /**
     * Decrypt data
     */
    private function Decrypt($data) {
        if (empty($data)) {
            return '';
        }
        
        $key = hex2bin($this->GetEncryptionKey());
        $iv = hex2bin(substr($this->GetEncryptionIV(), 0, 32));
        
        $decrypted = openssl_decrypt(
            base64_decode($data),
            $this->encryption_method,
            $key,
            0,
            $iv
        );
        
        return $decrypted;
    }
    
    /**
     * Get provider configuration
     */
    public function GetProviderConfig($provider) {
        $default_configs = array(
            'huggingface' => array(
                'model' => 'mistralai/Mistral-7B-Instruct-v0.2',
                'endpoint' => 'https://api-inference.huggingface.co/models/',
                'max_tokens' => 2000,
                'temperature' => 0.7
            ),
            'chatgpt' => array(
                'model' => 'gpt-3.5-turbo',
                'endpoint' => 'https://api.openai.com/v1/chat/completions',
                'max_tokens' => 2000,
                'temperature' => 0.7
            ),
            'claude' => array(
                'model' => 'claude-3-sonnet-20240229',
                'endpoint' => 'https://api.anthropic.com/v1/messages',
                'max_tokens' => 2000,
                'temperature' => 0.7
            ),
            'gemini' => array(
                'model' => 'gemini-pro',
                'endpoint' => 'https://generativelanguage.googleapis.com/v1/models/',
                'max_tokens' => 2000,
                'temperature' => 0.7
            ),
            'groq' => array(
                'model' => 'mixtral-8x7b-32768',
                'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
                'max_tokens' => 2000,
                'temperature' => 0.7
            )
        );
        
        // Get default config
        $config = isset($default_configs[$provider]) ? $default_configs[$provider] : array();
        
        // Override with user preferences
        $pref_key = 'provider_config_' . $provider;
        $user_config = $this->module->GetPreference($pref_key, '');
        
        if (!empty($user_config)) {
            $user_config = json_decode($user_config, true);
            if (is_array($user_config)) {
                $config = array_merge($config, $user_config);
            }
        }
        
        return $config;
    }
    
    /**
     * Set provider configuration
     */
    public function SetProviderConfig($provider, $config) {
        $pref_key = 'provider_config_' . $provider;
        $this->module->SetPreference($pref_key, json_encode($config, JSON_UNESCAPED_SLASHES));
    }
    
    /**
     * Get rate limit settings
     */
    public function GetRateLimitSettings() {
        return array(
            'requests_per_minute' => (int)$this->module->GetPreference('rate_limit_per_minute', 10),
            'requests_per_hour' => (int)$this->module->GetPreference('rate_limit_per_hour', 100),
            'requests_per_day' => (int)$this->module->GetPreference('rate_limit_per_day', 500)
        );
    }
    
    /**
     * Check if usage storage is in config.php mode
     */
    public function IsConfigPhpMode() {
        return defined('MAS_AI_USE_CONFIG_PHP') && MAS_AI_USE_CONFIG_PHP === true;
    }
}
?>


