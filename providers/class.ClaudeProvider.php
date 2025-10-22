<?php
/**
 * Claude (Anthropic) AI Provider
 * 
 * Anthropic Claude API integration
 * Requires API key
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

require_once(dirname(__FILE__) . '/../lib/class.AIProviderBase.php');

class ClaudeProvider extends AIProviderBase
{
    /**
     * Get provider name
     */
    protected function GetProviderName() {
        return 'claude';
    }
    
    /**
     * Perform generation
     */
    protected function DoGenerate($prompt, $options = array()) {
        $model = isset($options['model']) ? $options['model'] : $this->config['model'];
        $max_tokens = isset($options['max_tokens']) ? $options['max_tokens'] : $this->config['max_tokens'];
        $temperature = isset($options['temperature']) ? $options['temperature'] : $this->config['temperature'];
        
        $system_prompt = isset($options['system']) ? $options['system'] : $this->BuildSystemPrompt('content');
        
        $data = array(
            'model' => $model,
            'max_tokens' => $max_tokens,
            'temperature' => $temperature,
            'system' => $system_prompt,
            'messages' => array(
                array('role' => 'user', 'content' => $prompt)
            )
        );
        
        $headers = array(
            'Content-Type: application/json',
            'x-api-key: ' . $this->api_key,
            'anthropic-version: 2023-06-01'
        );
        
        try {
            $response = $this->MakeRequest($this->config['endpoint'], $data, $headers);
            
            if (!isset($response['content'][0]['text'])) {
                throw new Exception('Invalid response format from Claude API');
            }
            
            $content = $response['content'][0]['text'];
            
            return $this->FormatResponse($content, array(
                'model' => $model,
                'tokens_used' => isset($response['usage']['total_tokens']) ? $response['usage']['total_tokens'] : 0,
                'stop_reason' => isset($response['stop_reason']) ? $response['stop_reason'] : 'unknown'
            ));
            
        } catch (Exception $e) {
            $this->last_error = 'Claude API error: ' . $e->getMessage();
            return array(
                'success' => false,
                'error' => $this->last_error
            );
        }
    }
    
    /**
     * Get available models
     */
    public function GetAvailableModels() {
        return array(
            'claude-3-opus-20240229' => 'Claude 3 Opus (Most Capable)',
            'claude-3-sonnet-20240229' => 'Claude 3 Sonnet (Balanced)',
            'claude-3-haiku-20240307' => 'Claude 3 Haiku (Fast)',
            'claude-2.1' => 'Claude 2.1'
        );
    }
}
?>


