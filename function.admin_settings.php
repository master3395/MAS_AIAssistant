<?php
/**
 * Admin Settings Function
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return;
}

// Get Smarty instance
$smarty = cmsms()->GetSmarty();

// Handle form submission
if (isset($params['admin_settings_submit'])) {
    // Save admin settings
    $this->SetPreference('enable_logging', isset($params['enable_logging']) ? 1 : 0);
    $this->SetPreference('log_level', isset($params['log_level']) ? $params['log_level'] : 'info');
    $this->SetPreference('max_generations_per_hour', isset($params['max_generations_per_hour']) ? (int)$params['max_generations_per_hour'] : 100);
    $this->SetPreference('enable_caching', isset($params['enable_caching']) ? 1 : 0);
    $this->SetPreference('cache_duration', isset($params['cache_duration']) ? (int)$params['cache_duration'] : 3600);
    
    // Audit log
    $this->Audit(0, $this->Lang('friendlyname'), $this->Lang('admin_settings_updated'));
    
    // Redirect with success
    $this->Redirect($id, 'defaultadmin', $returnid, array(
        'activetab' => 'adminsettings',
        'msg' => 'admin_settings_updated'
    ));
}

// Get current settings
$settings = array(
    'enable_logging' => $this->GetPreference('enable_logging', 1),
    'log_level' => $this->GetPreference('log_level', 'info'),
    'max_generations_per_hour' => $this->GetPreference('max_generations_per_hour', 100),
    'enable_caching' => $this->GetPreference('enable_caching', 1),
    'cache_duration' => $this->GetPreference('cache_duration', 3600)
);

// Assign to Smarty
$smarty->assign('settings', $settings);
$smarty->assign('module', $this);
$smarty->assign('id', $id);
$smarty->assign('params', $params);
$smarty->assign('returnid', $returnid);

// Display template
echo $this->ProcessTemplate('admin_settings.tpl');
?>