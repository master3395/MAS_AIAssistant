<?php
/**
 * ChatGPT (OpenAI) AI Provider
 * 
 * OpenAI API integration for GPT models
 * Requires API key
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

require_once(dirname(__FILE__) . '/../lib/class.AIProviderBase.php');

class ChatGPTProvider extends AIProviderBase
{
    /**
     * Get provider name
     */
    protected function GetProviderName() {
        return 'chatgpt';
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
            'messages' => array(
                array('role' => 'system', 'content' => $system_prompt),
                array('role' => 'user', 'content' => $prompt)
            ),
            'max_tokens' => $max_tokens,
            'temperature' => $temperature
        );
        
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        );
        
        try {
            $response = $this->MakeRequest($this->config['endpoint'], $data, $headers);
            
            if (!isset($response['choices'][0]['message']['content'])) {
                throw new Exception('Invalid response format from OpenAI API');
            }
            
            $content = $response['choices'][0]['message']['content'];
            
            return $this->FormatResponse($content, array(
                'model' => $model,
                'tokens_used' => isset($response['usage']['total_tokens']) ? $response['usage']['total_tokens'] : 0,
                'finish_reason' => isset($response['choices'][0]['finish_reason']) ? $response['choices'][0]['finish_reason'] : 'unknown'
            ));
            
        } catch (Exception $e) {
            $this->last_error = 'OpenAI API error: ' . $e->getMessage();
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
            'gpt-4-turbo-preview' => 'GPT-4 Turbo (Most Capable)',
            'gpt-4' => 'GPT-4',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Recommended)',
            'gpt-3.5-turbo-16k' => 'GPT-3.5 Turbo 16K'
        );
    }
}
?>


