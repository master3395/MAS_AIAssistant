<?php
/**
 * SEO Optimizer for MAS AI Assistant
 * 
 * Analyzes content for SEO optimization
 * Provides recommendations and generates meta tags
 * 
 * @package MAS_AIAssistant
 */

if (!function_exists('cmsms')) exit;

class SEOOptimizer
{
    private $module;
    private $provider;
    
    /**
     * Constructor
     */
    public function __construct($module, $provider_name = 'huggingface') {
        $this->module = $module;
        $this->LoadProvider($provider_name);
    }
    
    /**
     * Load AI provider
     */
    private function LoadProvider($provider_name) {
        $class_name = ucfirst($provider_name) . 'Provider';
        $this->module->LoadProvider($provider_name);
        $this->provider = new $class_name($this->module);
    }
    
    /**
     * Analyze content for SEO
     */
    public function AnalyzeContent($content, $target_keyword = '') {
        $analysis = array(
            'score' => 0,
            'issues' => array(),
            'suggestions' => array()
        );
        
        // Basic SEO checks
        $word_count = str_word_count(strip_tags($content));
        $heading_count = substr_count($content, '<h2>') + substr_count($content, '<h3>');
        $paragraph_count = substr_count($content, '<p>');
        $link_count = substr_count($content, '<a ');
        
        // Word count check
        if ($word_count < 300) {
            $analysis['issues'][] = 'Content is too short (less than 300 words)';
            $analysis['suggestions'][] = 'Expand content to at least 300-500 words';
        } else {
            $analysis['score'] += 20;
        }
        
        // Heading structure
        if ($heading_count < 2) {
            $analysis['issues'][] = 'Not enough headings for structure';
            $analysis['suggestions'][] = 'Add more H2 and H3 headings to break up content';
        } else {
            $analysis['score'] += 20;
        }
        
        // Paragraph structure
        if ($paragraph_count < 3) {
            $analysis['issues'][] = 'Content needs more paragraphs';
            $analysis['suggestions'][] = 'Break content into smaller, readable paragraphs';
        } else {
            $analysis['score'] += 15;
        }
        
        // Keyword density (if target keyword provided)
        if (!empty($target_keyword)) {
            $keyword_count = substr_count(strtolower($content), strtolower($target_keyword));
            $density = ($word_count > 0) ? ($keyword_count / $word_count) * 100 : 0;
            
            if ($density < 0.5) {
                $analysis['issues'][] = 'Target keyword appears too infrequently';
                $analysis['suggestions'][] = "Include '{$target_keyword}' more naturally in the content";
            } elseif ($density > 3) {
                $analysis['issues'][] = 'Keyword stuffing detected';
                $analysis['suggestions'][] = "Reduce usage of '{$target_keyword}' to avoid over-optimization";
            } else {
                $analysis['score'] += 25;
            }
        }
        
        // Internal linking
        if ($link_count < 2) {
            $analysis['suggestions'][] = 'Add more internal links to improve site structure';
        } else {
            $analysis['score'] += 10;
        }
        
        // Readability check
        $analysis['score'] += 10; // Base score
        
        return $analysis;
    }
    
    /**
     * Generate SEO recommendations using AI
     */
    public function GenerateRecommendations($content, $target_keyword = '') {
        $prompt = "Analyze the following content for SEO optimization and provide specific recommendations:\n\n";
        $prompt .= substr(strip_tags($content), 0, 2000) . "\n\n";
        
        if (!empty($target_keyword)) {
            $prompt .= "Target keyword: {$target_keyword}\n\n";
        }
        
        $prompt .= "Provide:\n";
        $prompt .= "1. SEO score (0-100)\n";
        $prompt .= "2. Top 3-5 specific improvements\n";
        $prompt .= "3. Keyword suggestions\n";
        $prompt .= "4. Structure recommendations\n";
        
        $result = $this->provider->Generate($prompt, array(
            'system' => 'You are an SEO expert. Analyze content and provide actionable optimization recommendations.',
            'max_tokens' => 1000
        ));
        
        return $result;
    }
    
    /**
     * Generate schema markup
     */
    public function GenerateSchemaMarkup($content_type, $data) {
        $schemas = array(
            'article' => $this->GenerateArticleSchema($data),
            'webpage' => $this->GenerateWebPageSchema($data),
            'organization' => $this->GenerateOrganizationSchema($data),
            'breadcrumb' => $this->GenerateBreadcrumbSchema($data)
        );
        
        return isset($schemas[$content_type]) ? $schemas[$content_type] : '';
    }
    
    /**
     * Generate Article schema
     */
    private function GenerateArticleSchema($data) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => isset($data['title']) ? $data['title'] : '',
            'description' => isset($data['description']) ? $data['description'] : '',
            'author' => array(
                '@type' => 'Person',
                'name' => isset($data['author']) ? $data['author'] : 'Unknown'
            ),
            'datePublished' => isset($data['date']) ? $data['date'] : date('Y-m-d'),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => isset($data['site_name']) ? $data['site_name'] : ''
            )
        );
        
        return '<script type="application/ld+json">' . "\n" . 
               json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
               "\n</script>";
    }
    
    /**
     * Generate WebPage schema
     */
    private function GenerateWebPageSchema($data) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => isset($data['title']) ? $data['title'] : '',
            'description' => isset($data['description']) ? $data['description'] : '',
            'url' => isset($data['url']) ? $data['url'] : ''
        );
        
        return '<script type="application/ld+json">' . "\n" . 
               json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
               "\n</script>";
    }
    
    /**
     * Generate Organization schema
     */
    private function GenerateOrganizationSchema($data) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => isset($data['name']) ? $data['name'] : '',
            'url' => isset($data['url']) ? $data['url'] : '',
            'logo' => isset($data['logo']) ? $data['logo'] : ''
        );
        
        if (isset($data['social'])) {
            $schema['sameAs'] = $data['social'];
        }
        
        return '<script type="application/ld+json">' . "\n" . 
               json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
               "\n</script>";
    }
    
    /**
     * Generate Breadcrumb schema
     */
    private function GenerateBreadcrumbSchema($data) {
        if (!isset($data['items']) || !is_array($data['items'])) {
            return '';
        }
        
        $items = array();
        $position = 1;
        
        foreach ($data['items'] as $item) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => isset($item['url']) ? $item['url'] : ''
            );
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        );
        
        return '<script type="application/ld+json">' . "\n" . 
               json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
               "\n</script>";
    }
}
?>


