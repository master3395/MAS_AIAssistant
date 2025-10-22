<?php
/**
 * Security Helper for MAS AI Assistant
 * 
 * Input sanitization, XSS/SQL injection protection
 * Rate limiting and security validation
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

class SecurityHelper
{
    private $module;
    
    /**
     * Constructor
     */
    public function __construct($module) {
        $this->module = $module;
    }
    
    /**
     * Sanitize input string
     */
    public function SanitizeInput($input, $type = 'string') {
        if (is_null($input)) {
            return '';
        }
        
        switch ($type) {
            case 'int':
                return (int)$input;
                
            case 'float':
                return (float)$input;
                
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
                
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
                
            case 'html':
                // Allow safe HTML tags
                return strip_tags($input, '<p><br><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6>');
                
            case 'string':
            default:
                // Remove dangerous characters
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Sanitize array of inputs
     */
    public function SanitizeArray($array, $type = 'string') {
        if (!is_array($array)) {
            return array();
        }
        
        $sanitized = array();
        foreach ($array as $key => $value) {
            $safe_key = $this->SanitizeInput($key, 'string');
            
            if (is_array($value)) {
                $sanitized[$safe_key] = $this->SanitizeArray($value, $type);
            } else {
                $sanitized[$safe_key] = $this->SanitizeInput($value, $type);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Validate CSRF token
     */
    public function ValidateCSRFToken($token) {
        if (empty($token)) {
            return false;
        }
        
        $session_token = $this->GetCSRFToken();
        return hash_equals($session_token, $token);
    }
    
    /**
     * Generate CSRF token
     */
    public function GenerateCSRFToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION['mas_ai_csrf_token'])) {
            $_SESSION['mas_ai_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['mas_ai_csrf_token'];
    }
    
    /**
     * Get CSRF token
     */
    public function GetCSRFToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return isset($_SESSION['mas_ai_csrf_token']) ? $_SESSION['mas_ai_csrf_token'] : '';
    }
    
    /**
     * Check rate limit for user
     */
    public function CheckRateLimit($user_id = null) {
        if (is_null($user_id)) {
            $user_id = $this->GetCurrentUserId();
        }
        
        $db = cmsms()->GetDb();
        $prefix = cms_db_prefix();
        $now = time();
        
        // Get rate limit settings
        $this->module->LoadClass('ConfigManager');
        $config = new ConfigManager($this->module);
        $limits = $config->GetRateLimitSettings();
        
        // Check requests in last minute
        $query = "SELECT COUNT(*) as count FROM {$prefix}module_mas_ai_usage 
                  WHERE user_id = ? AND created_timestamp > ?";
        
        $one_minute_ago = $now - 60;
        $count = $db->GetOne($query, array($user_id, $one_minute_ago));
        
        if ($count >= $limits['requests_per_minute']) {
            return array(
                'allowed' => false,
                'reason' => 'rate_limit_minute',
                'limit' => $limits['requests_per_minute'],
                'reset_in' => 60
            );
        }
        
        return array('allowed' => true);
    }
    
    /**
     * Log request for rate limiting
     */
    public function LogRequest($user_id = null, $provider = '', $action = '') {
        if (is_null($user_id)) {
            $user_id = $this->GetCurrentUserId();
        }
        
        $db = cmsms()->GetDb();
        $prefix = cms_db_prefix();
        
        $query = "INSERT INTO {$prefix}module_mas_ai_usage 
                  (user_id, provider, action, created_timestamp) 
                  VALUES (?, ?, ?, ?)";
        
        $db->Execute($query, array($user_id, $provider, $action, time()));
    }
    
    /**
     * Get current user ID
     */
    private function GetCurrentUserId() {
        $user = cmsms()->GetUserOperations()->LoadUserByID();
        return $user ? $user->id : 0;
    }
    
    /**
     * Validate provider name
     */
    public function ValidateProvider($provider) {
        $valid_providers = array('huggingface', 'chatgpt', 'claude', 'cursorai', 'gemini', 'groq');
        return in_array($provider, $valid_providers);
    }
    
    /**
     * Sanitize prompt for AI
     */
    public function SanitizePrompt($prompt) {
        // Remove potentially malicious content
        $prompt = strip_tags($prompt);
        $prompt = trim($prompt);
        
        // Remove excessive whitespace
        $prompt = preg_replace('/\s+/', ' ', $prompt);
        
        // Limit length
        if (strlen($prompt) > 10000) {
            $prompt = substr($prompt, 0, 10000);
        }
        
        return $prompt;
    }
}
?>


