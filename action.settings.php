<?php
/**
 * Settings Page
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return;
}

// Get Smarty instance
$smarty = cmsms()->GetSmarty();

// Handle form submission
if (isset($params['settings_submit'])) {
    // Get form data
    $default_provider = isset($params['default_provider']) ? $params['default_provider'] : 'huggingface';
    $storage_method = isset($params['storage_method']) ? $params['storage_method'] : 'database';
    
    // Save preferences
    $this->SetPreference('default_provider', $default_provider);
    $this->SetPreference('storage_method', $storage_method);
    
    // Rate limiting settings
    if (isset($params['rate_limit_per_minute'])) {
        $this->SetPreference('rate_limit_per_minute', (int)$params['rate_limit_per_minute']);
    }
    
    // Save API keys
    $this->LoadClass('ConfigManager');
    $config = new ConfigManager($this);
    
    $providers_list = array('chatgpt', 'claude', 'gemini', 'groq');
    foreach ($providers_list as $prov) {
        $key_param = 'api_key_' . $prov;
        if (isset($params[$key_param])) {
            $key_value = trim($params[$key_param]);
            if (!empty($key_value) && $key_value !== '********') {
                $config->SetApiKey($prov, $key_value);
            }
        }
    }
    
    // Audit log
    $this->Audit(0, $this->Lang('friendlyname'), $this->Lang('settings_updated'));
    
    // Redirect with success
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'settings',
        'msg' => 'settings_updated'
    ));
    return;
}

// Display settings form
$smarty->assign('settings_form_start', $this->CreateFormStart($id, 'settings', $returnid));
$smarty->assign('settings_form_end', $this->CreateFormEnd());

// Default provider dropdown
$providers = $this->GetAvailableProviders();
$current_provider = $this->GetPreference('default_provider', 'huggingface');
$smarty->assign('default_provider_dropdown', $this->CreateInputDropdown(
    $id, 'default_provider', $providers, -1, $current_provider
));

// Storage method
$storage_methods = array(
    'database' => $this->Lang('storage_database'),
    'config_php' => $this->Lang('storage_config_php'),
    'both' => $this->Lang('storage_both')
);
$current_storage = $this->GetPreference('storage_method', 'database');
$smarty->assign('storage_method_dropdown', $this->CreateInputDropdown(
    $id, 'storage_method', $storage_methods, -1, $current_storage
));

// API Key inputs
$this->LoadClass('ConfigManager');
$config = new ConfigManager($this);

$api_keys = array();
foreach (array('chatgpt', 'claude', 'gemini', 'groq') as $prov) {
    $has_key = !empty($config->GetApiKey($prov));
    $display_value = $has_key ? '********' : '';
    $api_keys[$prov] = array(
        'input' => $this->CreateInputText($id, 'api_key_' . $prov, $display_value, 60),
        'configured' => $has_key
    );
}
$smarty->assign('api_keys', $api_keys);

// Rate limiting
$rate_limit = $this->GetPreference('rate_limit_per_minute', 10);
$smarty->assign('rate_limit_input', $this->CreateInputText($id, 'rate_limit_per_minute', $rate_limit, 5));

// Submit button
$smarty->assign('settings_submit', $this->CreateInputSubmit($id, 'settings_submit', $this->Lang('save')));
$smarty->assign('submit_hidden', $this->CreateInputHidden($id, 'settings_submit', '1'));

echo $this->ProcessTemplate('settings.tpl');
?>
