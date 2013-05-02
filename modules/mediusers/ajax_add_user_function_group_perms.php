<?php /* $Id: vw_idx_mediusers.php 7695 2009-12-23 09:10:10Z rhum1 $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 7695 $
* @author Romain Ollivier
*/

CCanDo::checkAdmin();

$mediuser = new CMediusers();
$mediusers = $mediuser->loadGroupList();
foreach ($mediusers as $mediuser) {
  $mediuser->insFunctionPermission();
  $mediuser->insGroupPermission();
}

CAppUI::stepAjax(count($mediusers)." utilisateurs vérifiés");
