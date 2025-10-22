<?php
/**
 * Groq AI Provider
 * 
 * Groq API integration (ultra-fast inference)
 * Requires API key (free tier available)
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

require_once(dirname(__FILE__) . '/../lib/class.AIProviderBase.php');

class GroqProvider extends AIProviderBase
{
    /**
     * Get provider name
     */
    protected function GetProviderName() {
        return 'groq';
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
                throw new Exception('Invalid response format from Groq API');
            }
            
            $content = $response['choices'][0]['message']['content'];
            
            return $this->FormatResponse($content, array(
                'model' => $model,
                'tokens_used' => isset($response['usage']['total_tokens']) ? $response['usage']['total_tokens'] : 0,
                'finish_reason' => isset($response['choices'][0]['finish_reason']) ? $response['choices'][0]['finish_reason'] : 'unknown'
            ));
            
        } catch (Exception $e) {
            $this->last_error = 'Groq API error: ' . $e->getMessage();
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
            'mixtral-8x7b-32768' => 'Mixtral 8x7B (Recommended)',
            'llama2-70b-4096' => 'Llama 2 70B',
            'gemma-7b-it' => 'Gemma 7B'
        );
    }
}
?>


