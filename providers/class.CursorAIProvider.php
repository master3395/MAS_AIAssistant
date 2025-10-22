<?php
/**
 * Cursor AI Provider
 * 
 * Note: Cursor AI doesn't have a public API yet
 * This is a placeholder for future implementation
 * Falls back to Claude for now
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

require_once(dirname(__FILE__) . '/../lib/class.AIProviderBase.php');

class CursorAIProvider extends AIProviderBase
{
    /**
     * Get provider name
     */
    protected function GetProviderName() {
        return 'cursorai';
    }
    
    /**
     * Perform generation
     * Currently uses Claude API as fallback
     */
    protected function DoGenerate($prompt, $options = array()) {
        // Cursor AI doesn't have a public API yet
        // Use Claude as fallback with code-focused system prompt
        $this->last_error = 'Cursor AI public API not yet available. Using Claude as fallback.';
        
        // Load Claude provider
        require_once(dirname(__FILE__) . '/class.ClaudeProvider.php');
        $claude = new ClaudeProvider($this->module);
        
        // Override system prompt for code generation
        $options['system'] = 'You are an expert code generator and developer. Create clean, well-documented code following best practices. For CMSMS, use proper Smarty syntax and CMSMS conventions.';
        
        return $claude->Generate($prompt, $options);
    }
    
    /**
     * Get available models
     */
    public function GetAvailableModels() {
        return array(
            'cursor-default' => 'Cursor AI (Fallback to Claude)'
        );
    }
}
?>


