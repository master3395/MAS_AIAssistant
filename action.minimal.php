<?php
/**
 * Minimal Working Admin Dashboard for MAS AI Assistant
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

// Dashboard Tab - Basic version
echo $this->StartTab("dashboard");
echo "<h2>AI Assistant Dashboard</h2>";
echo "<p>Welcome to MAS AI Assistant v2.0!</p>";

// Get basic stats safely
try {
    $stats = $this->GetGenerationStats();
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;'>";
    echo "<div style='padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;'>";
    echo "<h3>Total Generations</h3>";
    echo "<p style='font-size: 2em; font-weight: bold; color: #0073aa;'>" . $stats['total'] . "</p>";
    echo "</div>";
    echo "<div style='padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;'>";
    echo "<h3>Last 7 Days</h3>";
    echo "<p style='font-size: 2em; font-weight: bold; color: #46b450;'>" . $stats['last_week'] . "</p>";
    echo "</div>";
    echo "<div style='padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;'>";
    echo "<h3>Default Provider</h3>";
    echo "<p style='font-size: 1.2em; font-weight: bold; color: #f56e28;'>" . $this->GetDefaultProvider() . "</p>";
    echo "</div>";
    echo "</div>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading statistics: " . $e->getMessage() . "</p>";
}

// Provider status
echo "<h3>AI Provider Status</h3>";
try {
    $providers = $this->GetAvailableProviders();
    echo "<ul>";
    foreach ($providers as $key => $name) {
        $configured = $this->IsProviderConfigured($key);
        $status = $configured ? "✓ Configured" : "✗ Not Configured";
        $color = $configured ? "green" : "red";
        echo "<li style='color: $color;'>$name $status</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading providers: " . $e->getMessage() . "</p>";
}

echo $this->EndTab();

// Content Generation Tab - Basic version
echo $this->StartTab("content");
echo "<h2>Content Generator</h2>";
echo "<p>Content generation functionality will be restored step by step.</p>";
echo $this->EndTab();

// Design Generation Tab - Basic version
echo $this->StartTab("design");
echo "<h2>Design Generator</h2>";
echo "<p>Design generation functionality will be restored step by step.</p>";
echo $this->EndTab();

// Settings Tab - Basic version
echo $this->StartTab("settings");
echo "<h2>Settings</h2>";
echo "<p>Settings functionality will be restored step by step.</p>";
echo $this->EndTab();

// Custom Providers Tab - Basic version
echo $this->StartTab("customproviders");
echo "<h2>Custom Providers</h2>";
echo "<p>Custom providers functionality will be restored step by step.</p>";
echo $this->EndTab();

// Batch Generation Tab - Basic version
echo $this->StartTab("batch");
echo "<h2>Batch Generation</h2>";
echo "<p>Batch generation functionality will be restored step by step.</p>";
echo $this->EndTab();

// Content History Tab - Basic version
echo $this->StartTab("history");
echo "<h2>Content History</h2>";
echo "<p>Content history functionality will be restored step by step.</p>";
echo $this->EndTab();

// Admin Settings Tab - Basic version
echo $this->StartTab("adminsettings");
echo "<h2>Admin Settings</h2>";
echo "<p>Admin settings functionality will be restored step by step.</p>";
echo $this->EndTab();

// Donations Tab
if ($this->ShowDonationsTab()) {
    echo $this->StartTab("donations");
    echo "<h2>Donations</h2>";
    echo "<p>Thank you for supporting MAS AI Assistant!</p>";
    echo $this->EndTab();
}

echo $this->EndTabContent();
?>
