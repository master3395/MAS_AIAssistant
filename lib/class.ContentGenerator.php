<?php
/**
 * Content Generator for MAS AI Assistant
 * 
 * Generates page content, blog posts, meta descriptions
 * Uses AI providers to create high-quality content
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

class ContentGenerator
{
    private $module;
    private $provider;
    private $custom_provider_name;
    
    /**
     * Constructor
     */
    public function __construct($module, $provider_name = 'huggingface', $custom_provider_name = null) {
        $this->module = $module;
        $this->custom_provider_name = $custom_provider_name;
        $this->LoadProvider($provider_name);
    }
    
    /**
     * Load AI provider
     */
    private function LoadProvider($provider_name) {
        // Handle custom providers
        if ($this->custom_provider_name) {
            $this->module->LoadClass('CustomProvider');
            $this->provider = new CustomProvider($this->module, $this->custom_provider_name);
            return;
        }
        
        // Handle built-in providers
        $class_name = ucfirst($provider_name) . 'Provider';
        $this->module->LoadProvider($provider_name);
        $this->provider = new $class_name($this->module);
    }
    
    /**
     * Generate page content
     */
    public function GeneratePageContent($topic, $options = array()) {
        $word_count = isset($options['word_count']) ? $options['word_count'] : 500;
        $tone = isset($options['tone']) ? $options['tone'] : 'professional';
        $keywords = isset($options['keywords']) ? $options['keywords'] : array();
        
        $prompt = $this->BuildContentPrompt($topic, $word_count, $tone, $keywords, 'page');
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an expert content writer. Create SEO-optimized, engaging content with proper HTML formatting.',
            'max_tokens' => min($word_count * 3, 4000)
        ));
        
        if ($result['success']) {
            $result['content'] = $this->ProcessGeneratedContent($result['content']);
        }
        
        return $result;
    }
    
    /**
     * Generate blog post
     */
    public function GenerateBlogPost($topic, $options = array()) {
        $word_count = isset($options['word_count']) ? $options['word_count'] : 800;
        $tone = isset($options['tone']) ? $options['tone'] : 'conversational';
        $keywords = isset($options['keywords']) ? $options['keywords'] : array();
        
        $prompt = $this->BuildContentPrompt($topic, $word_count, $tone, $keywords, 'blog');
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an expert blog writer. Create engaging, informative blog posts with a clear structure, subheadings, and proper HTML formatting.',
            'max_tokens' => min($word_count * 3, 4000)
        ));
        
        if ($result['success']) {
            $result['content'] = $this->ProcessGeneratedContent($result['content']);
        }
        
        return $result;
    }
    
    /**
     * Generate meta description
     */
    public function GenerateMetaDescription($content_or_title, $max_length = 160) {
        $prompt = "Write a compelling meta description (max {$max_length} characters) for the following content:\n\n{$content_or_title}\n\nThe meta description should be SEO-friendly, include relevant keywords, and encourage clicks. Only output the meta description text, nothing else.";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 100
        ));
        
        if ($result['success']) {
            // Clean and truncate meta description
            $meta = strip_tags($result['content']);
            $meta = trim($meta, " \n\r\t\v\0\"'");
            if (strlen($meta) > $max_length) {
                $meta = substr($meta, 0, $max_length - 3) . '...';
            }
            $result['content'] = $meta;
        }
        
        return $result;
    }
    
    /**
     * Generate meta keywords
     */
    public function GenerateMetaKeywords($content, $max_keywords = 10) {
        $prompt = "Extract up to {$max_keywords} relevant SEO keywords from the following content:\n\n{$content}\n\nProvide only the keywords separated by commas, nothing else.";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 100
        ));
        
        if ($result['success']) {
            $keywords = strip_tags($result['content']);
            $keywords = trim($keywords, " \n\r\t\v\0,");
            $result['content'] = $keywords;
        }
        
        return $result;
    }
    
    /**
     * Generate SEO-friendly title
     */
    public function GenerateTitle($topic, $max_length = 60) {
        $prompt = "Create a compelling, SEO-friendly title (max {$max_length} characters) for content about: {$topic}\n\nThe title should be catchy, include keywords, and be under {$max_length} characters. Only output the title, nothing else.";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 50
        ));
        
        if ($result['success']) {
            $title = strip_tags($result['content']);
            $title = trim($title, " \n\r\t\v\0\"'");
            if (strlen($title) > $max_length) {
                $title = substr($title, 0, $max_length - 3) . '...';
            }
            $result['content'] = $title;
        }
        
        return $result;
    }
    
    /**
     * Rewrite/improve existing content
     */
    public function RewriteContent($content, $style = 'improve') {
        $styles = array(
            'improve' => 'Improve and enhance the following content while maintaining its core message. Make it more engaging, fix grammar, and improve flow.',
            'simplify' => 'Simplify the following content to make it easier to understand. Use shorter sentences and simpler words.',
            'expand' => 'Expand the following content with more details, examples, and explanations.',
            'shorten' => 'Condense the following content while keeping the key points.',
            'professional' => 'Rewrite the following content in a more professional tone.',
            'casual' => 'Rewrite the following content in a more casual, friendly tone.'
        );
        
        $instruction = isset($styles[$style]) ? $styles[$style] : $styles['improve'];
        $prompt = $instruction . "\n\n" . $content;
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => strlen($content) * 2
        ));
        
        if ($result['success']) {
            $result['content'] = $this->ProcessGeneratedContent($result['content']);
        }
        
        return $result;
    }
    
    /**
     * Build content generation prompt
     */
    private function BuildContentPrompt($topic, $word_count, $tone, $keywords, $type) {
        $prompt = "Write a {$type} about: {$topic}\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Approximately {$word_count} words\n";
        $prompt .= "- Tone: {$tone}\n";
        
        if (!empty($keywords)) {
            $prompt .= "- Include these keywords naturally: " . implode(', ', $keywords) . "\n";
        }
        
        $prompt .= "- Use proper HTML formatting (p, h2, h3, strong, em, ul, ol, li tags)\n";
        $prompt .= "- Make it SEO-friendly and engaging\n";
        $prompt .= "- Include clear structure with headings\n";
        $prompt .= "- Only output the content HTML, no explanations\n";
        
        return $prompt;
    }
    
    /**
     * Process generated content
     */
    private function ProcessGeneratedContent($content) {
        // Remove markdown code blocks if present
        $content = preg_replace('/```html\n(.*?)\n```/s', '$1', $content);
        $content = preg_replace('/```\n(.*?)\n```/s', '$1', $content);
        
        // Clean up excessive whitespace
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        
        // Ensure basic HTML structure
        if (strpos($content, '<p>') === false && strpos($content, '<h') === false) {
            // Wrap plain text in paragraphs
            $paragraphs = explode("\n\n", trim($content));
            $content = '';
            foreach ($paragraphs as $para) {
                $para = trim($para);
                if (!empty($para)) {
                    $content .= '<p>' . $para . '</p>' . "\n";
                }
            }
        }
        
        return trim($content);
    }
}
?>


