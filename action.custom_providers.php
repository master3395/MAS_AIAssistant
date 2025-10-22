<?php
/**
 * Custom AI Providers Management
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Get Smarty instance
$smarty = cmsms()->GetSmarty();

// Handle form submissions
if (isset($params['add_provider_submit'])) {
    $provider_name = trim($params['provider_name']);
    $api_key = trim($params['api_key']);
    $endpoint = trim($params['endpoint']);
    $model = trim($params['model']);
    $request_format = $params['request_format'];
    $display_name = trim($params['display_name']);
    $description = trim($params['description']);
    
    // Validate inputs
    if (empty($provider_name) || empty($api_key) || empty($endpoint)) {
        $this->Redirect($id, 'custom_providers', $returnid, array(
            'error' => 'missing_required_fields'
        ));
        return;
    }
    
    // Sanitize provider name (alphanumeric and underscore only)
    $provider_name = preg_replace('/[^a-zA-Z0-9_]/', '', $provider_name);
    if (empty($provider_name)) {
        $this->Redirect($id, 'custom_providers', $returnid, array(
            'error' => 'invalid_provider_name'
        ));
        return;
    }
    
    // Prepare configuration
    $config = array(
        'name' => $display_name ?: $provider_name,
        'description' => $description,
        'api_key' => $api_key,
        'endpoint' => $endpoint,
        'model' => $model,
        'request_format' => $request_format,
        'headers' => array(),
        'created_date' => date('Y-m-d H:i:s')
    );
    
    // Add custom request/response format if provided
    if ($request_format === 'custom') {
        $config['custom_request'] = json_decode($params['custom_request'] ?? '{}', true);
        $config['response_path'] = $params['response_path'] ?? 'content';
    }
    
    // Save configuration
    $this->SetPreference('custom_provider_' . $provider_name, json_encode($config));
    
    // Add to available providers list
    $custom_providers = json_decode($this->GetPreference('custom_providers_list', '[]'), true);
    if (!in_array($provider_name, $custom_providers)) {
        $custom_providers[] = $provider_name;
        $this->SetPreference('custom_providers_list', json_encode($custom_providers));
    }
    
    $this->Redirect($id, 'custom_providers', $returnid, array(
        'msg' => 'provider_added'
    ));
    return;
}

if (isset($params['delete_provider'])) {
    $provider_name = $params['delete_provider'];
    
    // Remove configuration
    $this->RemovePreference('custom_provider_' . $provider_name);
    
    // Remove from list
    $custom_providers = json_decode($this->GetPreference('custom_providers_list', '[]'), true);
    $custom_providers = array_diff($custom_providers, array($provider_name));
    $this->SetPreference('custom_providers_list', json_encode(array_values($custom_providers)));
    
    $this->Redirect($id, 'custom_providers', $returnid, array(
        'msg' => 'provider_deleted'
    ));
    return;
}

if (isset($params['test_provider'])) {
    $provider_name = $params['test_provider'];
    
    try {
        $this->LoadProvider('custom', $provider_name);
        $provider = new CustomProvider($this, $provider_name);
        $test_result = $provider->TestConnection();
        
        $this->Redirect($id, 'custom_providers', $returnid, array(
            'msg' => $test_result['success'] ? 'test_success' : 'test_failed',
            'test_message' => $test_result['message']
        ));
    } catch (Exception $e) {
        $this->Redirect($id, 'custom_providers', $returnid, array(
            'msg' => 'test_failed',
            'test_message' => $e->getMessage()
        ));
    }
    return;
}

// Get custom providers list
$custom_providers = json_decode($this->GetPreference('custom_providers_list', '[]'), true);
$providers_data = array();

foreach ($custom_providers as $provider_name) {
    try {
        $config_json = $this->GetPreference('custom_provider_' . $provider_name, '');
        $config = json_decode($config_json, true);
        
        if ($config) {
            $providers_data[] = array(
                'name' => $provider_name,
                'display_name' => $config['name'],
                'description' => $config['description'],
                'endpoint' => $config['endpoint'],
                'model' => $config['model'],
                'format' => $config['request_format'],
                'created' => $config['created_date'] ?? 'Unknown'
            );
        }
    } catch (Exception $e) {
        // Skip invalid providers
    }
}

// Display form
$smarty->assign('form_start', $this->CreateFormStart($id, 'custom_providers', $returnid));
$smarty->assign('form_end', $this->CreateFormEnd());

// Provider name input
$smarty->assign('provider_name_input', $this->CreateInputText($id, 'provider_name', '', 30));

// Display name input
$smarty->assign('display_name_input', $this->CreateInputText($id, 'display_name', '', 30));

// Description input
$smarty->assign('description_input', $this->CreateTextArea(false, $id, '', 'description', '', '', '', '', 50, 3));

// API key input
$smarty->assign('api_key_input', $this->CreateInputText($id, 'api_key', '', 60));

// Endpoint input
$smarty->assign('endpoint_input', $this->CreateInputText($id, 'endpoint', '', 60));

// Model input
$smarty->assign('model_input', $this->CreateInputText($id, 'model', '', 30));

// Request format dropdown
$request_formats = array(
    'openai' => 'OpenAI Compatible',
    'anthropic' => 'Anthropic Claude',
    'huggingface' => 'Hugging Face',
    'custom' => 'Custom Format'
);
$smarty->assign('request_format_dropdown', $this->CreateInputDropdown(
    $id, 'request_format', $request_formats, -1, 'openai'
));

// Custom request format textarea
$smarty->assign('custom_request_textarea', $this->CreateTextArea(false, $id, '', 'custom_request', '', '', '', '', 60, 5));

// Response path input
$smarty->assign('response_path_input', $this->CreateInputText($id, 'response_path', 'content', 30));

// Submit button
$smarty->assign('add_provider_button', $this->CreateInputSubmit($id, 'add_provider_submit', $this->Lang('add_provider')));
$smarty->assign('add_provider_hidden', $this->CreateInputHidden($id, 'add_provider_submit', '1'));

// Providers list
$smarty->assign('providers_data', $providers_data);

// Messages
if (isset($params['msg'])) {
    $smarty->assign('message', $this->Lang($params['msg']));
    $smarty->assign('message_type', in_array($params['msg'], array('provider_added', 'test_success')) ? 'success' : 'error');
}

if (isset($params['test_message'])) {
    $smarty->assign('test_message', $params['test_message']);
}

echo $this->ProcessTemplate('custom_providers.tpl');
?>
