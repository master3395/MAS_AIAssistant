<?php
/**
 * Template Generator for MAS AI Assistant
 * 
 * Generates CMSMS templates with Smarty tags
 * Creates headers, footers, and complete themes
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

class TemplateGenerator
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
     * Generate CMSMS page template
     */
    public function GeneratePageTemplate($description, $options = array()) {
        $include_navigation = isset($options['navigation']) ? $options['navigation'] : true;
        $include_breadcrumbs = isset($options['breadcrumbs']) ? $options['breadcrumbs'] : true;
        
        $prompt = "Create a CMSMS page template for: {$description}\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Use Smarty syntax\n";
        $prompt .= "- Include CMSMS tags: {\$content}, {\$metadata}, {\$sitename}\n";
        
        if ($include_navigation) {
            $prompt .= "- Add navigation menu using {menu}\n";
        }
        
        if ($include_breadcrumbs) {
            $prompt .= "- Add breadcrumbs using {breadcrumbs}\n";
        }
        
        $prompt .= "- Make it responsive with Bootstrap 5\n";
        $prompt .= "- Include proper HTML5 structure\n";
        $prompt .= "- Add SEO meta tags\n";
        $prompt .= "- Output only the template code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an expert CMSMS template developer. Create well-structured Smarty templates following CMSMS best practices.',
            'max_tokens' => 3000
        ));
        
        if ($result['success']) {
            $result['template'] = $this->ExtractCode($result['content']);
            $result['template'] = $this->EnhanceCMSMSTags($result['template']);
        }
        
        return $result;
    }
    
    /**
     * Generate header template
     */
    public function GenerateHeaderTemplate($style = 'modern') {
        $prompt = "Create a CMSMS header template with {$style} style.\n\n";
        $prompt .= "Include:\n";
        $prompt .= "- Site logo using {\$sitename}\n";
        $prompt .= "- Navigation menu using {menu}\n";
        $prompt .= "- Responsive mobile menu\n";
        $prompt .= "- Search functionality\n";
        $prompt .= "- Bootstrap 5 classes\n";
        $prompt .= "- Output only the Smarty template code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 2000
        ));
        
        if ($result['success']) {
            $result['template'] = $this->ExtractCode($result['content']);
            $result['template'] = $this->EnhanceCMSMSTags($result['template']);
        }
        
        return $result;
    }
    
    /**
     * Generate footer template
     */
    public function GenerateFooterTemplate($columns = 3) {
        $prompt = "Create a CMSMS footer template with {$columns} columns.\n\n";
        $prompt .= "Include:\n";
        $prompt .= "- Copyright notice with {\$sitename}\n";
        $prompt .= "- Footer navigation/links\n";
        $prompt .= "- Social media icons\n";
        $prompt .= "- Contact information\n";
        $prompt .= "- Responsive design\n";
        $prompt .= "- Bootstrap 5 classes\n";
        $prompt .= "- Output only the Smarty template code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 2000
        ));
        
        if ($result['success']) {
            $result['template'] = $this->ExtractCode($result['content']);
            $result['template'] = $this->EnhanceCMSMSTags($result['template']);
        }
        
        return $result;
    }
    
    /**
     * Generate News module template
     */
    public function GenerateNewsTemplate($type = 'summary') {
        $prompt = "Create a CMSMS News module {$type} template.\n\n";
        $prompt .= "Use News module Smarty variables:\n";
        $prompt .= "- {\$items} - array of news entries\n";
        $prompt .= "- {\$entry->title}, {\$entry->content}, {\$entry->summary}\n";
        $prompt .= "- {\$entry->postdate}, {\$entry->author}\n";
        $prompt .= "- {\$entry->moreurl}, {\$entry->category}\n";
        $prompt .= "- Make it responsive and visually appealing\n";
        $prompt .= "- Output only the Smarty template code\n";
        
        $result = $this->provider->Generate($prompt, array(
            'max_tokens' => 2000
        ));
        
        if ($result['success']) {
            $result['template'] = $this->ExtractCode($result['content']);
        }
        
        return $result;
    }
    
    /**
     * Enhance CMSMS tags in template
     */
    private function EnhanceCMSMSTags($template) {
        // Ensure proper CMSMS variable syntax
        $template = str_replace('$content', '{$content}', $template);
        $template = str_replace('$metadata', '{$metadata}', $template);
        $template = str_replace('$sitename', '{$sitename}', $template);
        
        // Add missing DOCTYPE if needed
        if (strpos($template, '<!DOCTYPE') === false && strpos($template, '<html') !== false) {
            $template = "<!DOCTYPE html>\n" . $template;
        }
        
        return $template;
    }
    
    /**
     * Extract code from response
     */
    private function ExtractCode($content) {
        // Remove markdown code blocks
        $patterns = array(
            '/```smarty\n(.*?)\n```/s',
            '/```html\n(.*?)\n```/s',
            '/```\n(.*?)\n```/s'
        );
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return trim($content);
    }
}
?>


