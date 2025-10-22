<?php
/**
 * Uninstallation Method for MAS AI Assistant
 */

if (!function_exists('cmsms')) exit;

$db = cmsms()->GetDb();
$prefix = cms_db_prefix();
$dict = NewDataDictionary($db);

// Remove permissions
$this->RemovePermission('Use MAS_AIAssistant');

// Drop database tables
$sqlarray = $dict->DropTableSQL($prefix . 'module_mas_ai_generations');
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL($prefix . 'module_mas_ai_usage');
$dict->ExecuteSQLArray($sqlarray);

// Remove all preferences
$this->RemovePreference('default_provider');
$this->RemovePreference('storage_method');
$this->RemovePreference('rate_limit_per_minute');
$this->RemovePreference('rate_limit_per_hour');
$this->RemovePreference('rate_limit_per_day');
$this->RemovePreference('encryption_key');
$this->RemovePreference('encryption_iv');
$this->RemovePreference('hidedonationstab');

// Remove API keys
$providers = array('huggingface', 'chatgpt', 'claude', 'cursorai', 'gemini', 'groq');
foreach ($providers as $provider) {
    $this->RemovePreference('api_key_' . $provider);
    $this->RemovePreference('provider_config_' . $provider);
}

// Audit log
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
?>

