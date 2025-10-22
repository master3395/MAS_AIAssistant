<?php
/**
 * Design Generator for MAS AI Assistant
 * 
 * Generates HTML/CSS designs, responsive layouts
 * Creates component-based designs
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

class DesignGenerator
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
     * Generate complete page layout
     */
    public function GeneratePageLayout($description, $options = array()) {
        $framework = isset($options['framework']) ? $options['framework'] : 'bootstrap5';
        $sections = isset($options['sections']) ? $options['sections'] : array('header', 'main', 'footer');
        
        $prompt = "Create a complete HTML page layout for: {$description}\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Use {$framework} framework\n";
        $prompt .= "- Include these sections: " . implode(', ', $sections) . "\n";
        $prompt .= "- Make it fully responsive\n";
        $prompt .= "- Use semantic HTML5\n";
        $prompt .= "- Include proper CSS classes\n";
        $prompt .= "- Add accessibility attributes (ARIA)\n";
        $prompt .= "- Output only the HTML code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an expert web designer. Create modern, responsive, accessible HTML layouts following best practices.',
            'max_tokens' => 3000
        ));
        
        if ($result['success']) {
            $result['html'] = $this->ExtractCode($result['content'], 'html');
        }
        
        return $result;
    }
    
    /**
     * Generate CSS stylesheet
     */
    public function GenerateCSS($description, $options = array()) {
        $color_scheme = isset($options['colors']) ? $options['colors'] : 'modern blue';
        $type = isset($options['type']) ? $options['type'] : 'full';
        
        $prompt = "Create CSS stylesheet for: {$description}\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Color scheme: {$color_scheme}\n";
        $prompt .= "- Modern, clean design\n";
        $prompt .= "- Responsive (mobile-first)\n";
        $prompt .= "- Include hover effects and transitions\n";
        $prompt .= "- Use CSS variables for colors\n";
        $prompt .= "- Well-organized and commented\n";
        $prompt .= "- Output only the CSS code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an expert CSS developer. Create modern, maintainable stylesheets with best practices.',
            'max_tokens' => 2500
        ));
        
        if ($result['success']) {
            $result['css'] = $this->ExtractCode($result['content'], 'css');
        }
        
        return $result;
    }
    
    /**
     * Generate HTML component
     */
    public function GenerateComponent($component_type, $options = array()) {
        $components = array(
            'navbar' => 'responsive navigation bar with dropdown menus',
            'hero' => 'hero section with heading, description, and call-to-action',
            'card' => 'card component with image, title, description, and button',
            'form' => 'contact form with validation',
            'footer' => 'footer with links, social media, and copyright',
            'gallery' => 'image gallery with lightbox',
            'testimonial' => 'testimonial/review card',
            'pricing' => 'pricing table with features',
            'faq' => 'FAQ accordion',
            'cta' => 'call-to-action section'
        );
        
        $description = isset($components[$component_type]) ? $components[$component_type] : $component_type;
        
        $prompt = "Create a {$description} HTML component.\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Modern, responsive design\n";
        $prompt .= "- Bootstrap 5 compatible classes\n";
        $prompt .= "- Semantic HTML\n";
        $prompt .= "- Accessible (ARIA labels)\n";
        $prompt .= "- Include inline CSS if needed\n";
        $prompt .= "- Output only the HTML code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an expert front-end developer. Create reusable, accessible HTML components.',
            'max_tokens' => 1500
        ));
        
        if ($result['success']) {
            $result['html'] = $this->ExtractCode($result['content'], 'html');
        }
        
        return $result;
    }
    
    /**
     * Generate responsive grid layout
     */
    public function GenerateGridLayout($columns, $description = '') {
        $prompt = "Create a responsive {$columns}-column grid layout";
        
        if (!empty($description)) {
            $prompt .= " for: {$description}";
        }
        
        $prompt .= "\n\nRequirements:\n";
        $prompt .= "- Use CSS Grid or Flexbox\n";
        $prompt .= "- Mobile: 1 column, Tablet: 2 columns, Desktop: {$columns} columns\n";
        $prompt .= "- Include example content\n";
        $prompt .= "- Modern, clean styling\n";
        $prompt .= "- Output complete HTML with inline CSS\n";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 2000
        ));
        
        if ($result['success']) {
            $result['html'] = $this->ExtractCode($result['content'], 'html');
        }
        
        return $result;
    }
    
    /**
     * Extract code from AI response
     */
    private function ExtractCode($content, $type = 'html') {
        // Remove markdown code blocks
        $pattern = '/```' . $type . '\n(.*?)\n```/s';
        if (preg_match($pattern, $content, $matches)) {
            return trim($matches[1]);
        }
        
        // Try generic code block
        $pattern = '/```\n(.*?)\n```/s';
        if (preg_match($pattern, $content, $matches)) {
            return trim($matches[1]);
        }
        
        // Return as-is if no code blocks found
        return trim($content);
    }
}
?>


