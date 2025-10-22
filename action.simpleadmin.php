<?php
/**
 * Simplified Admin Dashboard for MAS AI Assistant
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Handle hiding donations tab
if (isset($params["hidedonationssubmit"])) {
    $this->SetPreference("hidedonationstab", $this->GetVersion());
}

// Handle success messages
if (!empty($params['msg'])) {
    echo $this->ShowMessage($this->Lang($params['msg']));
}

// Get current tab
$activetab = isset($params["activetab"]) ? $params["activetab"] : "";

// Start tabs
echo $this->StartTabHeaders();
echo $this->SetTabHeader("dashboard", $this->Lang('tab_dashboard'), ($activetab == "" || $activetab == "dashboard"));
echo $this->SetTabHeader("content", $this->Lang('tab_content'), ($activetab == "content"));
echo $this->SetTabHeader("design", $this->Lang('tab_design'), ($activetab == "design"));
echo $this->SetTabHeader("settings", $this->Lang('tab_settings'), ($activetab == "settings"));
echo $this->SetTabHeader("customproviders", $this->Lang('tab_custom_providers'), ($activetab == "customproviders"));
echo $this->SetTabHeader("batch", $this->Lang('tab_batch_generation'), ($activetab == "batch"));
echo $this->SetTabHeader("history", $this->Lang('tab_content_history'), ($activetab == "history"));
echo $this->SetTabHeader("adminsettings", $this->Lang('tab_adminsettings'), ($activetab == "adminsettings"));

if ($this->ShowDonationsTab()) {
    echo $this->SetTabHeader("donations", $this->Lang('tab_donations'), ($activetab == "donations"));
}

echo $this->EndTabHeaders();
echo $this->StartTabContent();

// Dashboard Tab
echo $this->StartTab("dashboard");
$smarty->assign('module', $this);

// Get generation statistics with error handling
try {
    $stats = $this->GetGenerationStats();
    $smarty->assign('total_generations', $stats['total']);
    $smarty->assign('last_week_generations', $stats['last_week']);
} catch (Exception $e) {
    $smarty->assign('total_generations', 0);
    $smarty->assign('last_week_generations', 0);
}

// Get available providers with error handling
try {
    $providers = $this->GetAvailableProviders();
    $smarty->assign('providers', $providers);
} catch (Exception $e) {
    $smarty->assign('providers', array('huggingface' => 'Hugging Face (Free)'));
}

// Get default provider with error handling
try {
    $default_provider = $this->GetDefaultProvider();
    $smarty->assign('default_provider', $default_provider);
} catch (Exception $e) {
    $smarty->assign('default_provider', 'huggingface');
}

// Check which providers are configured
$configured_providers = array();
try {
    foreach (array_keys($providers) as $provider) {
        if ($this->IsProviderConfigured($provider)) {
            $configured_providers[] = $provider;
        }
    }
} catch (Exception $e) {
    $configured_providers = array('huggingface');
}
$smarty->assign('configured_providers', $configured_providers);

echo $this->ProcessTemplate('admin_dashboard.tpl');
echo $this->EndTab();

// Content Generation Tab
echo $this->StartTab("content");
$smarty->assign('content_form_start', $this->CreateFormStart($id, 'generate_content', $returnid, 'post'));
$smarty->assign('content_form_end', $this->CreateFormEnd());

// Provider dropdown
$smarty->assign('provider_dropdown', $this->CreateInputDropdown(
    $id, 'provider', $providers, -1, $default_provider
));

// Content type dropdown
$content_types = array(
    'page' => $this->Lang('content_type_page'),
    'blog' => $this->Lang('content_type_blog'),
    'meta' => $this->Lang('content_type_meta')
);
$smarty->assign('content_type_dropdown', $this->CreateInputDropdown(
    $id, 'content_type', $content_types, -1, 'page'
));

// Topic input
$smarty->assign('topic_input', $this->CreateInputText($id, 'topic', '', 80));

// Word count input
$smarty->assign('word_count_input', $this->CreateInputText($id, 'word_count', '500', 10));

// Keywords input
$smarty->assign('keywords_input', $this->CreateInputText($id, 'keywords', '', 80));

// Submit button
$smarty->assign('generate_button', $this->CreateInputSubmit($id, 'submit', $this->Lang('generate')));

echo $this->ProcessTemplate('content_generator.tpl');
echo $this->EndTab();

// Design Generation Tab
echo $this->StartTab("design");
$smarty->assign('design_form_start', $this->CreateFormStart($id, 'generate_design', $returnid, 'post'));
$smarty->assign('design_form_end', $this->CreateFormEnd());

// Design type dropdown
$design_types = array(
    'layout' => $this->Lang('design_type_layout'),
    'component' => $this->Lang('design_type_component'),
    'css' => $this->Lang('design_type_css'),
    'template' => $this->Lang('design_type_template')
);
$smarty->assign('design_type_dropdown', $this->CreateInputDropdown(
    $id, 'design_type', $design_types, -1, 'layout'
));

// Description input
$smarty->assign('description_input', $this->CreateTextArea(false, $id, '', 'description', '', '', '', '', 80, 5));

// Provider dropdown
$smarty->assign('design_provider_dropdown', $this->CreateInputDropdown(
    $id, 'provider', $providers, -1, $default_provider
));

// Submit button
$smarty->assign('design_generate_button', $this->CreateInputSubmit($id, 'submit', $this->Lang('generate')));

echo $this->ProcessTemplate('design_generator.tpl');
echo $this->EndTab();

// Settings Tab - Simple version
echo $this->StartTab("settings");
echo "<h3>Settings</h3>";
echo "<p>Settings functionality will be available once the module is fully working.</p>";
echo $this->EndTab();

// Custom Providers Tab - Simple version
echo $this->StartTab("customproviders");
echo "<h3>Custom Providers</h3>";
echo "<p>Custom providers functionality will be available once the module is fully working.</p>";
echo $this->EndTab();

// Batch Generation Tab - Simple version
echo $this->StartTab("batch");
echo "<h3>Batch Generation</h3>";
echo "<p>Batch generation functionality will be available once the module is fully working.</p>";
echo $this->EndTab();

// Content History Tab - Simple version
echo $this->StartTab("history");
echo "<h3>Content History</h3>";
echo "<p>Content history functionality will be available once the module is fully working.</p>";
echo $this->EndTab();

// Admin Settings Tab - Simple version
echo $this->StartTab("adminsettings");
echo "<h3>Admin Settings</h3>";
echo "<p>Admin settings functionality will be available once the module is fully working.</p>";
echo $this->EndTab();

// Donations Tab
if ($this->ShowDonationsTab()) {
    echo $this->StartTab("donations");
    include(dirname(__FILE__) . "/function.donations.php");
    echo $this->EndTab();
}

echo $this->EndTabContent();
?>
