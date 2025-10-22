<?php
/**
 * Admin Settings Save Action for MAS AI Assistant
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Handle showing donations tab again
if (isset($params["showdonationstab"]) && $params["showdonationstab"] == "1") {
    // Remove the preference to show the donations tab again
    $this->RemovePreference("hidedonationstab");
    $msg = "Settings updated successfully";
} else {
    // Hide the donations tab
    $this->SetPreference("hidedonationstab", $this->GetVersion());
    $msg = "Settings updated successfully";
}

// put mention into the admin log
$this->Audit(0, 
    $this->Lang('friendlyname'), 
    "Settings updated");

// redirect back to admin with message
$this->Redirect($id, 'defaultadmin', $returnid, array('msg' => $msg, 'activetab' => 'adminsettings'));

?>