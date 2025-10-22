<?php
/**
 * Installation Method for MAS AI Assistant
 */

if (!function_exists('cmsms')) exit;

$db = cmsms()->GetDb();
$prefix = cms_db_prefix();
$dict = NewDataDictionary($db);

// Create permissions
$this->CreatePermission('Use MAS_AIAssistant', 'Use MAS AI Assistant module');

// Create database tables

// Table 1: AI Generations (stores generated content)
$fields = "
    id I KEY AUTO,
    type C(50),
    provider C(50),
    prompt X,
    result X,
    metadata X,
    user_id I,
    created_date T
";

$taboptarray = array('mysql' => 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
$sqlarray = $dict->CreateTableSQL($prefix . 'module_mas_ai_generations', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// Create index on created_date
$sqlarray = $dict->CreateIndexSQL(
    $prefix . 'idx_mas_ai_gen_date',
    $prefix . 'module_mas_ai_generations',
    'created_date'
);
$dict->ExecuteSQLArray($sqlarray);

// Table 2: AI Usage (for rate limiting)
$fields = "
    id I KEY AUTO,
    user_id I,
    provider C(50),
    action C(100),
    created_timestamp I
";

$sqlarray = $dict->CreateTableSQL($prefix . 'module_mas_ai_usage', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// Create index on user_id and timestamp
$sqlarray = $dict->CreateIndexSQL(
    $prefix . 'idx_mas_ai_usage_user',
    $prefix . 'module_mas_ai_usage',
    'user_id,created_timestamp'
);
$dict->ExecuteSQLArray($sqlarray);

// Set default preferences
$this->SetPreference('default_provider', 'huggingface');
$this->SetPreference('storage_method', 'database');
$this->SetPreference('rate_limit_per_minute', 10);
$this->SetPreference('rate_limit_per_hour', 100);
$this->SetPreference('rate_limit_per_day', 500);

// Create to-do folder
$module_path = dirname(__FILE__);
$todo_path = $module_path . '/to-do';
if (!is_dir($todo_path)) {
    mkdir($todo_path, 0755, true);
}

// Audit log
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
?>

