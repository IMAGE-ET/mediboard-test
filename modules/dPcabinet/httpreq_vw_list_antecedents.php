<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id  = mbGetValueFromGetOrSession("patient_id", 0);
$_is_anesth  = mbGetValueFromGetOrSession("_is_anesth", null);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsAntecedents();
$patient->loadRefsTraitements();
$patient->loadRefsAddictions();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"    , $patient);
$smarty->assign("_is_anesth" , $_is_anesth);

$smarty->display("inc_list_ant.tpl");

?>