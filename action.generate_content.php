<?php
/**
 * Content Generation Action
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Get parameters
$provider = isset($params['provider']) ? $params['provider'] : $this->GetDefaultProvider();
$content_type = isset($params['content_type']) ? $params['content_type'] : 'page';
$topic = isset($params['topic']) ? trim($params['topic']) : '';
$word_count = isset($params['word_count']) ? (int)$params['word_count'] : 500;
$keywords = isset($params['keywords']) ? trim($params['keywords']) : '';

// Validate inputs
if (empty($topic)) {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'content',
        'error' => 'topic_required'
    ));
    return;
}

// Security check
$this->LoadClass('SecurityHelper');
$security = new SecurityHelper($this);

if (!$security->ValidateProvider($provider)) {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'content',
        'error' => 'invalid_provider'
    ));
    return;
}

// Check rate limit
$rate_check = $security->CheckRateLimit();
if (!$rate_check['allowed']) {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'content',
        'error' => 'rate_limit_exceeded',
        'reset_in' => $rate_check['reset_in']
    ));
    return;
}

// Load content generator
$this->LoadClass('ContentGenerator');

// Handle custom providers
if (strpos($provider, 'custom_') === 0) {
    $custom_provider_name = substr($provider, 7); // Remove 'custom_' prefix
    $generator = new ContentGenerator($this, $provider, $custom_provider_name);
} else {
    $generator = new ContentGenerator($this, $provider);
}

// Prepare options
$options = array(
    'word_count' => $word_count
);

if (!empty($keywords)) {
    $keywords_array = array_map('trim', explode(',', $keywords));
    $options['keywords'] = $keywords_array;
}

// Generate content
$result = null;
switch ($content_type) {
    case 'blog':
        $result = $generator->GenerateBlogPost($topic, $options);
        break;
    
    case 'meta':
        $result = $generator->GenerateMetaDescription($topic);
        break;
    
    case 'page':
    default:
        $result = $generator->GeneratePageContent($topic, $options);
        break;
}

// Log the request
$security->LogRequest(null, $provider, 'content_generation');

// Save generation to database
if ($result['success']) {
    $db = cmsms()->GetDb();
    $prefix = cms_db_prefix();
    
    $query = "INSERT INTO {$prefix}module_mas_ai_generations 
              (type, provider, prompt, result, created_date) 
              VALUES (?, ?, ?, ?, NOW())";
    
    $db->Execute($query, array(
        $content_type,
        $provider,
        $topic,
        $result['content']
    ));
}

// Audit log
$this->Audit(0, $this->Lang('friendlyname'), 
    'Generated ' . $content_type . ' content using ' . $provider);

// Redirect with result
if ($result['success']) {
    // Store result in session for display
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['mas_ai_generated_content'] = $result['content'];
    
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'content',
        'msg' => 'generation_success',
        'show_result' => 1
    ));
} else {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'content',
        'error' => 'generation_failed',
        'error_msg' => isset($result['error']) ? $result['error'] : 'Unknown error'
    ));
}
?>
