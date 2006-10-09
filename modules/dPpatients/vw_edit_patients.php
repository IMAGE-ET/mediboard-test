<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Droit de lecture dPsante400
$moduleSante400 = CModule::getInstalled("dPsante400");
$canReadSante400 = $moduleSante400 ? $moduleSante400->canRead() : false;

$patient_id = mbGetValueFromGetOrSession("patient_id");
$dialog     = mbGetValueFromGet("dialog",0);
$name       = mbGetValueFromGet("name");
$firstName  = mbGetValueFromGet("firstName");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
}

// Vérification de l'existence de doublons
$textSiblings = null;
$patientSib = null;
if($created = mbGetValueFromGet("created", 0)){
  $patientSib = new CPatient();
  $where["patient_id"] = "= '$created'";
  $patientSib->loadObject($where);
  $siblings = $patientSib->getSiblings();
  if(count($siblings) == 0) {
  	$textSiblings = null;
  	$patientSib = null;
  	if($dialog)
  	  $AppUI->redirect("m=dPpatients&a=pat_selector&dialog=1&name=$patient->nom&firstName=$patient->prenom");
  	else
  	  $AppUI->redirect("m=dPpatients&tab=vw_idx_patients&id=$created&nom=&prenom=");
  }
  else {
  	$textSiblings = "Risque de doublons :";
    foreach($siblings as $key => $value) {
      $textSiblings .= "\n>> ".$value->nom." ".$value->prenom.
                       " né(e) le ".$value->naissance.
                       " habitant ".$value->adresse." ".$value->cp." ".$value->ville;
    }
    $textSiblings .= "\nVoulez-vous tout de même le créer ?";
  }
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("canReadSante400", $canReadSante400);
$smarty->assign("patientSib"  , $patientSib  );
$smarty->assign("patient"     , $patient     );
$smarty->assign("created"     , $created     );
$smarty->assign("textSiblings", $textSiblings);

$smarty->display("vw_edit_patients.tpl");
?>