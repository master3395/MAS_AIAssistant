<?php
/**
 * Google Gemini AI Provider
 * 
 * Google Gemini API integration
 * Requires API key (free tier available)
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

require_once(dirname(__FILE__) . '/../lib/class.AIProviderBase.php');

class GeminiProvider extends AIProviderBase
{
    /**
     * Get provider name
     */
    protected function GetProviderName() {
        return 'gemini';
    }
    
    /**
     * Perform generation
     */
    protected function DoGenerate($prompt, $options = array()) {
        $model = isset($options['model']) ? $options['model'] : $this->config['model'];
        $temperature = isset($options['temperature']) ? $options['temperature'] : $this->config['temperature'];
        
        $url = $this->config['endpoint'] . $model . ':generateContent?key=' . $this->api_key;
        
        $data = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => $temperature,
                'maxOutputTokens' => isset($options['max_tokens']) ? $options['max_tokens'] : $this->config['max_tokens']
            )
        );
        
        $headers = array(
            'Content-Type: application/json'
        );
        
        try {
            $response = $this->MakeRequest($url, $data, $headers);
            
            if (!isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                throw new Exception('Invalid response format from Gemini API');
            }
            
            $content = $response['candidates'][0]['content']['parts'][0]['text'];
            
            return $this->FormatResponse($content, array(
                'model' => $model,
                'finish_reason' => isset($response['candidates'][0]['finishReason']) ? $response['candidates'][0]['finishReason'] : 'unknown'
            ));
            
        } catch (Exception $e) {
            $this->last_error = 'Gemini API error: ' . $e->getMessage();
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
            'gemini-pro' => 'Gemini Pro (Recommended)',
            'gemini-pro-vision' => 'Gemini Pro Vision (Multimodal)'
        );
    }
}
?>


