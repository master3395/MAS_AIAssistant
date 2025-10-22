<?php
/**
 * Simple Working Admin Dashboard for MAS AI Assistant
 * No includes to avoid redirect loops
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

// Content Generation Tab
echo $this->StartTab("content");
echo "<h2>Content Generator</h2>";
echo "<p>Generate AI-powered content for your website.</p>";

// Simple content generation form
echo "<form method='post' action='" . $this->CreateLink($id, 'generate_content', $returnid) . "'>";
echo "<div style='margin: 20px 0;'>";
echo "<label for='content_type'>Content Type:</label><br>";
echo "<select name='content_type' id='content_type' style='width: 200px; padding: 5px;'>";
echo "<option value='page'>Page Content</option>";
echo "<option value='blog'>Blog Post</option>";
echo "<option value='article'>Article</option>";
echo "<option value='product'>Product Description</option>";
echo "</select>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label for='topic'>Topic/Subject:</label><br>";
echo "<input type='text' name='topic' id='topic' style='width: 400px; padding: 5px;' placeholder='Enter your topic here...'>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label for='keywords'>Keywords (optional):</label><br>";
echo "<input type='text' name='keywords' id='keywords' style='width: 400px; padding: 5px;' placeholder='Enter keywords separated by commas...'>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label for='word_count'>Word Count:</label><br>";
echo "<select name='word_count' id='word_count' style='width: 200px; padding: 5px;'>";
echo "<option value='300'>300 words</option>";
echo "<option value='500'>500 words</option>";
echo "<option value='750'>750 words</option>";
echo "<option value='1000'>1000 words</option>";
echo "</select>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<input type='submit' name='generate_content' value='Generate Content' style='background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;'>";
echo "</div>";
echo "</form>";

echo $this->EndTab();

// Design Generation Tab
echo $this->StartTab("design");
echo "<h2>Design Generator</h2>";
echo "<p>Generate AI-powered design elements and templates.</p>";

// Simple design generation form
echo "<form method='post' action='" . $this->CreateLink($id, 'generate_design', $returnid) . "'>";
echo "<div style='margin: 20px 0;'>";
echo "<label for='design_type'>Design Type:</label><br>";
echo "<select name='design_type' id='design_type' style='width: 200px; padding: 5px;'>";
echo "<option value='layout'>Page Layout</option>";
echo "<option value='component'>UI Component</option>";
echo "<option value='css'>CSS Styles</option>";
echo "<option value='template'>Template</option>";
echo "</select>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label for='description'>Description:</label><br>";
echo "<textarea name='description' id='description' style='width: 400px; height: 100px; padding: 5px;' placeholder='Describe the design you want...'></textarea>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<input type='submit' name='generate_design' value='Generate Design' style='background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;'>";
echo "</div>";
echo "</form>";

echo $this->EndTab();

// Settings Tab
echo $this->StartTab("settings");
echo "<h2>Settings</h2>";
echo "<p>Configure your AI assistant settings and API keys.</p>";

// Simple settings form
echo "<form method='post' action='" . $this->CreateLink($id, 'settings', $returnid) . "'>";
echo "<div style='margin: 20px 0;'>";
echo "<label for='default_provider'>Default Provider:</label><br>";
echo "<select name='default_provider' id='default_provider' style='width: 200px; padding: 5px;'>";
echo "<option value='huggingface'>Hugging Face (Free)</option>";
echo "<option value='chatgpt'>ChatGPT</option>";
echo "<option value='claude'>Claude</option>";
echo "<option value='gemini'>Google Gemini</option>";
echo "</select>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<label for='storage_method'>Storage Method:</label><br>";
echo "<select name='storage_method' id='storage_method' style='width: 200px; padding: 5px;'>";
echo "<option value='database'>Database</option>";
echo "<option value='config'>Config File</option>";
echo "</select>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<input type='submit' name='save_settings' value='Save Settings' style='background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;'>";
echo "</div>";
echo "</form>";

echo $this->EndTab();

// Custom Providers Tab
echo $this->StartTab("customproviders");
echo "<h2>Custom Providers</h2>";
echo "<p>Add and manage custom AI providers.</p>";
echo "<p><em>Custom provider functionality will be available in the next update.</em></p>";
echo $this->EndTab();

// Batch Generation Tab
echo $this->StartTab("batch");
echo "<h2>Batch Generation</h2>";
echo "<p>Generate multiple pieces of content at once.</p>";
echo "<p><em>Batch generation functionality will be available in the next update.</em></p>";
echo $this->EndTab();

// Content History Tab
echo $this->StartTab("history");
echo "<h2>Content History</h2>";
echo "<p>View and manage your content generation history.</p>";
echo "<p><em>Content history functionality will be available in the next update.</em></p>";
echo $this->EndTab();

// Admin Settings Tab
echo $this->StartTab("adminsettings");
echo "<div style='border:1px solid #CCC; padding:10px; margin:10px 0;'>";
echo "<h3>Admin Settings</h3>";

// Check if donations tab is currently hidden
$donationsHidden = ($this->GetPreference("hidedonationstab") == $this->GetVersion());

echo $this->CreateFormStart($id, "admin_settings_save");
echo "<p>";
echo "<label for='showdonationstab'>Show Donations Tab:</label><br/>";
echo $this->CreateInputCheckbox($id, "showdonationstab", "1", !$donationsHidden);
echo "</p>";

echo "<p>";
echo $this->CreateInputSubmit($id, "submit", "Save Settings");
echo "</p>";
echo $this->CreateFormEnd();

echo "</div>";
echo $this->EndTab();

// Donations Tab
if ($this->ShowDonationsTab()) {
    echo $this->StartTab("donations");
    
    // Sponsors section
    echo "<div style='border:1px solid black;padding:10px;'>";
    echo "<strong>Current sponsors, thank you for your support!</strong><br/>";
    echo "<a href='https://newstargeted.com/' target='_blank'>News Targeted</a><br/>";
    echo "</div><br/>";
    
    // Donation text
    echo "A lot of time and effort has been put into creating this module. Please consider a small donation (5€ for instance, or what you can spare) using the PayPal-button below, especially if you use this module in a commercial context. If you donate more than 30€ you can have a link to your company on this page, if you wish to. Send me an email about what you would like shown and I will put it in for the next version. Thank you!<br/><br/>";
    
    // PayPal donation form
    echo "<form action='https://paypal.me/KimBS?locale.x=en_US&country.x=NO' method='post' target='_blank'>";
    echo "<input type='image' src='https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif' name='submit' alt='PayPal - The safer, easier way to pay online!' />";
    echo "</form><br/><br/>";
    
    // Hide donations form
    echo $this->CreateFormStart($id, "defaultadmin");
    echo $this->CreateInputSubmit($id, "hidedonationssubmit", "Hide donations tab");
    echo $this->CreateFormEnd();
    
    echo $this->EndTab();
}

echo $this->EndTabContent();
?>