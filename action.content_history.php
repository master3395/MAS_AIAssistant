<?php
/**
 * Content History and Versioning System
 * Track and manage content generations over time
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Get Smarty instance
$smarty = cmsms()->GetSmarty();

// Handle actions
$action = isset($params['history_action']) ? $params['history_action'] : '';

if ($action === 'restore') {
    $version_id = isset($params['version_id']) ? (int)$params['version_id'] : 0;
    
    if ($version_id > 0) {
        $db = cmsms()->GetDb();
        $prefix = cms_db_prefix();
        
        $query = "SELECT * FROM {$prefix}module_mas_ai_generations WHERE id = ?";
        $result = $db->GetRow($query, array($version_id));
        
        if ($result) {
            // Store in session for restoration
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['mas_ai_restore_content'] = $result['result'];
            $_SESSION['mas_ai_restore_prompt'] = $result['prompt'];
            
            $this->Redirect($id, 'content_history', $returnid, array(
                'msg' => 'content_restored'
            ));
        }
    }
    return;
}

if ($action === 'delete') {
    $version_id = isset($params['version_id']) ? (int)$params['version_id'] : 0;
    
    if ($version_id > 0) {
        $db = cmsms()->GetDb();
        $prefix = cms_db_prefix();
        
        $query = "DELETE FROM {$prefix}module_mas_ai_generations WHERE id = ?";
        $db->Execute($query, array($version_id));
        
        $this->Redirect($id, 'content_history', $returnid, array(
            'msg' => 'version_deleted'
        ));
    }
    return;
}

if ($action === 'compare') {
    $version1 = isset($params['version1']) ? (int)$params['version1'] : 0;
    $version2 = isset($params['version2']) ? (int)$params['version2'] : 0;
    
    if ($version1 > 0 && $version2 > 0) {
        $db = cmsms()->GetDb();
        $prefix = cms_db_prefix();
        
        $query = "SELECT * FROM {$prefix}module_mas_ai_generations WHERE id IN (?, ?) ORDER BY id";
        $results = $db->GetArray($query, array($version1, $version2));
        
        if (count($results) === 2) {
            $smarty->assign('compare_version1', $results[0]);
            $smarty->assign('compare_version2', $results[1]);
            $smarty->assign('show_comparison', true);
        }
    }
}

// Get filter parameters
$filter_provider = isset($params['filter_provider']) ? $params['filter_provider'] : '';
$filter_type = isset($params['filter_type']) ? $params['filter_type'] : '';
$filter_date_from = isset($params['filter_date_from']) ? $params['filter_date_from'] : '';
$filter_date_to = isset($params['filter_date_to']) ? $params['filter_date_to'] : '';
$search_term = isset($params['search_term']) ? trim($params['search_term']) : '';

// Build query
$db = cmsms()->GetDb();
$prefix = cms_db_prefix();

$where_conditions = array();
$params_array = array();

if (!empty($filter_provider)) {
    $where_conditions[] = "provider = ?";
    $params_array[] = $filter_provider;
}

if (!empty($filter_type)) {
    $where_conditions[] = "type = ?";
    $params_array[] = $filter_type;
}

if (!empty($filter_date_from)) {
    $where_conditions[] = "created_date >= ?";
    $params_array[] = $filter_date_from . ' 00:00:00';
}

if (!empty($filter_date_to)) {
    $where_conditions[] = "created_date <= ?";
    $params_array[] = $filter_date_to . ' 23:59:59';
}

if (!empty($search_term)) {
    $where_conditions[] = "(prompt LIKE ? OR result LIKE ?)";
    $search_param = '%' . $search_term . '%';
    $params_array[] = $search_param;
    $params_array[] = $search_param;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM {$prefix}module_mas_ai_generations $where_clause";
$total_count = $db->GetOne($count_query, $params_array);

// Pagination
$page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;
$total_pages = ceil($total_count / $per_page);

// Get history data
$query = "SELECT * FROM {$prefix}module_mas_ai_generations 
          $where_clause 
          ORDER BY created_date DESC 
          LIMIT $per_page OFFSET $offset";

$history_data = $db->GetArray($query, $params_array);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_generations,
    COUNT(DISTINCT provider) as providers_used,
    COUNT(DISTINCT type) as types_used,
    AVG(LENGTH(result)) as avg_content_length,
    MAX(created_date) as last_generation
    FROM {$prefix}module_mas_ai_generations";

$stats = $db->GetRow($stats_query);

// Get provider usage stats
$provider_stats_query = "SELECT provider, COUNT(*) as count 
                         FROM {$prefix}module_mas_ai_generations 
                         GROUP BY provider 
                         ORDER BY count DESC";
$provider_stats = $db->GetArray($provider_stats_query);

// Get type usage stats
$type_stats_query = "SELECT type, COUNT(*) as count 
                     FROM {$prefix}module_mas_ai_generations 
                     GROUP BY type 
                     ORDER BY count DESC";
$type_stats = $db->GetArray($type_stats_query);

// Assign data to template
$smarty->assign('history_data', $history_data);
$smarty->assign('stats', $stats);
$smarty->assign('provider_stats', $provider_stats);
$smarty->assign('type_stats', $type_stats);
$smarty->assign('total_count', $total_count);
$smarty->assign('current_page', $page);
$smarty->assign('total_pages', $total_pages);
$smarty->assign('per_page', $per_page);

// Filter options
$providers = $this->GetAvailableProviders();
$smarty->assign('provider_filter_options', $providers);

$content_types = array(
    'page' => $this->Lang('content_type_page'),
    'blog' => $this->Lang('content_type_blog'),
    'meta' => $this->Lang('content_type_meta'),
    'design_layout' => $this->Lang('design_type_layout'),
    'design_component' => $this->Lang('design_type_component'),
    'design_css' => $this->Lang('design_type_css'),
    'design_template' => $this->Lang('design_type_template'),
    'batch_page' => $this->Lang('batch_content_type_page'),
    'batch_blog' => $this->Lang('batch_content_type_blog'),
    'batch_meta' => $this->Lang('batch_content_type_meta')
);
$smarty->assign('type_filter_options', $content_types);

// Current filters
$smarty->assign('current_filters', array(
    'provider' => $filter_provider,
    'type' => $filter_type,
    'date_from' => $filter_date_from,
    'date_to' => $filter_date_to,
    'search' => $search_term
));

// Messages
if (isset($params['msg'])) {
    $smarty->assign('message', $this->Lang($params['msg']));
    $smarty->assign('message_type', 'success');
}

// Show restored content if available
if (isset($_SESSION['mas_ai_restore_content'])) {
    $smarty->assign('restored_content', $_SESSION['mas_ai_restore_content']);
    $smarty->assign('restored_prompt', $_SESSION['mas_ai_restore_prompt']);
    unset($_SESSION['mas_ai_restore_content']);
    unset($_SESSION['mas_ai_restore_prompt']);
}

echo $this->ProcessTemplate('content_history.tpl');
?>
