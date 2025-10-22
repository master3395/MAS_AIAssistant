<?php
/**
 * Minimal Admin Dashboard for MAS AI Assistant - Debug Version
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>MAS AI Assistant - Debug Mode</h2>";

// Test basic functionality
echo "<h3>Module Status</h3>";
echo "<p>Module Name: " . $this->GetName() . "</p>";
echo "<p>Friendly Name: " . $this->GetFriendlyName() . "</p>";
echo "<p>Version: " . $this->GetVersion() . "</p>";

// Test providers
echo "<h3>Available Providers</h3>";
try {
    $providers = $this->GetAvailableProviders();
    echo "<p>Found " . count($providers) . " providers:</p>";
    echo "<ul>";
    foreach ($providers as $key => $name) {
        echo "<li>$key: $name</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error getting providers: " . $e->getMessage() . "</p>";
}

// Test default provider
echo "<h3>Default Provider</h3>";
try {
    $default = $this->GetDefaultProvider();
    echo "<p>Default: $default</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error getting default provider: " . $e->getMessage() . "</p>";
}

// Test generation stats
echo "<h3>Generation Statistics</h3>";
try {
    $stats = $this->GetGenerationStats();
    echo "<p>Total: " . $stats['total'] . "</p>";
    echo "<p>Last 7 days: " . $stats['last_week'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error getting stats: " . $e->getMessage() . "</p>";
}

echo "<h3>Debug Complete</h3>";
echo "<p>If you can see this page with the CMSMS admin interface (navbar, footer, styles), then the basic module is working.</p>";
?>
