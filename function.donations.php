<?php
/**
 * Donations Function
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return;
}

// Get Smarty instance
$smarty = cmsms()->GetSmarty();

// Assign to Smarty
$smarty->assign('module', $this);
$smarty->assign('id', $id);
$smarty->assign('params', $params);
$smarty->assign('returnid', $returnid);

// Display template
echo $this->ProcessTemplate('donations.tpl');
?>