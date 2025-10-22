<?php
/**
 * Design Generation Action
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Get parameters
$provider = isset($params['provider']) ? $params['provider'] : $this->GetDefaultProvider();
$design_type = isset($params['design_type']) ? $params['design_type'] : 'layout';
$description = isset($params['description']) ? trim($params['description']) : '';

// Validate inputs
if (empty($description)) {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'design',
        'error' => 'description_required'
    ));
    return;
}

// Security check
$this->LoadClass('SecurityHelper');
$security = new SecurityHelper($this);

if (!$security->ValidateProvider($provider)) {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'design',
        'error' => 'invalid_provider'
    ));
    return;
}

// Check rate limit
$rate_check = $security->CheckRateLimit();
if (!$rate_check['allowed']) {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'design',
        'error' => 'rate_limit_exceeded',
        'reset_in' => $rate_check['reset_in']
    ));
    return;
}

// Generate design based on type
$result = null;

// Handle custom providers
$custom_provider_name = null;
if (strpos($provider, 'custom_') === 0) {
    $custom_provider_name = substr($provider, 7); // Remove 'custom_' prefix
}

switch ($design_type) {
    case 'css':
        $this->LoadClass('DesignGenerator');
        $generator = new DesignGenerator($this, $provider, $custom_provider_name);
        $result = $generator->GenerateCSS($description);
        break;
    
    case 'component':
        $this->LoadClass('DesignGenerator');
        $generator = new DesignGenerator($this, $provider, $custom_provider_name);
        $result = $generator->GenerateComponent($description);
        break;
    
    case 'template':
        $this->LoadClass('TemplateGenerator');
        $generator = new TemplateGenerator($this, $provider, $custom_provider_name);
        $result = $generator->GeneratePageTemplate($description);
        break;
    
    case 'layout':
    default:
        $this->LoadClass('DesignGenerator');
        $generator = new DesignGenerator($this, $provider, $custom_provider_name);
        $result = $generator->GeneratePageLayout($description);
        break;
}

// Log the request
$security->LogRequest(null, $provider, 'design_generation');

// Save generation to database
if ($result['success']) {
    $db = cmsms()->GetDb();
    $prefix = cms_db_prefix();
    
    $content = isset($result['html']) ? $result['html'] : 
               (isset($result['css']) ? $result['css'] : 
               (isset($result['template']) ? $result['template'] : $result['content']));
    
    $query = "INSERT INTO {$prefix}module_mas_ai_generations 
              (type, provider, prompt, result, created_date) 
              VALUES (?, ?, ?, ?, NOW())";
    
    $db->Execute($query, array(
        'design_' . $design_type,
        $provider,
        $description,
        $content
    ));
}

// Audit log
$this->Audit(0, $this->Lang('friendlyname'), 
    'Generated ' . $design_type . ' using ' . $provider);

// Redirect with result
if ($result['success']) {
    // Store result in session for display
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['mas_ai_generated_design'] = $content;
    $_SESSION['mas_ai_design_type'] = $design_type;
    
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'design',
        'msg' => 'generation_success',
        'show_result' => 1
    ));
} else {
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'design',
        'error' => 'generation_failed',
        'error_msg' => isset($result['error']) ? $result['error'] : 'Unknown error'
    ));
}
?>

