<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision:  $
* @author
*/

$object_guid = CValue::get("object_guid");

// Chargement du patient
$patient = CMbObject::loadFromGuid($object_guid);

// Chargement de son dossier médical
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;

// Chargement des allergies   
$allergies = array();
if($dossier_medical->_id){
  $dossier_medical->loadRefsAllergies();
  $allergies = $dossier_medical->_ref_allergies;
}

$smarty = new CSmartyDP();
$smarty->assign("allergies", $allergies);
$smarty->display("inc_vw_allergies.tpl");

?>