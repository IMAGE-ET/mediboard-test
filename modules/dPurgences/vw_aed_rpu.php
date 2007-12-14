<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$group = new CGroups();
$group->load($g);
$user = new CMediusers();
$listResponsables = $user->loadUsers(PERM_READ, $group->service_urgences_id);

$rpu_id = mbGetValueFromGetOrSession("rpu_id");
$rpu    = new CRPU;
$rpu->load($rpu_id);
if($rpu->_id) {
  $sejour  = $rpu->_ref_sejour;
  $patient = $sejour->_ref_patient;
} else {
  $rpu->_responsable_id = $AppUI->user_id;
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
  $patient              = new CPatient;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rpu"             , $rpu);
$smarty->assign("sejour"          , $sejour);
$smarty->assign("patient"         , $patient);
$smarty->assign("listResponsables", $listResponsables);

$smarty->display("vw_aed_rpu.tpl");
?>