<?php
/**
 * Hugging Face AI Provider
 * 
 * Free AI provider using Hugging Face Inference API
 * No API key required for basic usage
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

require_once(dirname(__FILE__) . '/../lib/class.AIProviderBase.php');

class HuggingFaceProvider extends AIProviderBase
{
    /**
     * Get provider name
     */
    protected function GetProviderName() {
        return 'huggingface';
    }
    
    /**
     * Hugging Face doesn't require API key for basic usage
     */
    protected function RequiresApiKey() {
        return false; // Optional API key for better rate limits
    }
    
    /**
     * Perform generation
     */
    protected function DoGenerate($prompt, $options = array()) {
        $model = isset($options['model']) ? $options['model'] : $this->config['model'];
        $max_tokens = isset($options['max_tokens']) ? $options['max_tokens'] : $this->config['max_tokens'];
        $temperature = isset($options['temperature']) ? $options['temperature'] : $this->config['temperature'];
        
        $url = $this->config['endpoint'] . $model;
        
        $data = array(
            'inputs' => $prompt,
            'parameters' => array(
                'max_new_tokens' => $max_tokens,
                'temperature' => $temperature,
                'return_full_text' => false,
                'do_sample' => true
            ),
            'options' => array(
                'wait_for_model' => true
            )
        );
        
        $headers = array(
            'Content-Type: application/json'
        );
        
        // Add API key if available
        if (!empty($this->api_key)) {
            $headers[] = 'Authorization: Bearer ' . $this->api_key;
        }
        
        try {
            $response = $this->MakeRequest($url, $data, $headers);
            
            // Handle response format
            if (isset($response[0]['generated_text'])) {
                $content = $response[0]['generated_text'];
            } elseif (isset($response['generated_text'])) {
                $content = $response['generated_text'];
            } else {
                throw new Exception('Unexpected response format from Hugging Face API');
            }
            
            return $this->FormatResponse($content, array(
                'model' => $model,
                'tokens_used' => strlen($content) // Approximate
            ));
            
        } catch (Exception $e) {
            $this->last_error = 'Hugging Face API error: ' . $e->getMessage();
            return array(
                'success' => false,
                'error' => $this->last_error
            );
        }
    }
    
    /**
     * Test connection to Hugging Face API
     */
    public function TestConnection() {
        $test_prompt = "Hello, world!";
        $result = $this->Generate($test_prompt, array('max_tokens' => 50));
        return $result['success'];
    }
    
    /**
     * Get available models
     */
    public function GetAvailableModels() {
        return array(
            'mistralai/Mistral-7B-Instruct-v0.2' => 'Mistral 7B Instruct (Recommended)',
            'meta-llama/Meta-Llama-3-8B-Instruct' => 'Llama 3 8B Instruct',
            'microsoft/Phi-3-mini-4k-instruct' => 'Phi-3 Mini (Fast)',
            'google/flan-t5-xxl' => 'FLAN-T5 XXL',
            'bigscience/bloomz' => 'BLOOM-Z'
        );
    }
}
?>


