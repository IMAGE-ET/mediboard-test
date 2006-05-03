<?php /* $Id: vw_edit_patients.php,v 1.19 2006/04/21 16:56:38 mytto Exp $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 1.19 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id");
$dialog = dPgetParam($_GET, "dialog");
$name = dPgetParam($_GET, "name");
$firstName = dPgetParam($_GET, "firstName");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

if (!$patient_id) {
  $patient->nom = $name;
  $patient->prenom = $firstName;
}

// Vérification de l'existence de doublons
$textSiblings = null;
$patientSib = null;
if($created = dPgetParam($_GET, 'created', 0)){
  $patientSib = new CPatient();
  $where["patient_id"] = "= '$created'";
  $patientSib->loadObject($where);
  $siblings = $patientSib->getSiblings();
  if(count($siblings) == 0) {
  	$textSiblings = null;
  	$patientSib = null;
  	if($dialog)
  	  $AppUI->redirect( "m=dPpatients&a=pat_selector&dialog=1&name=$patient->nom&firstName=$patient->prenom" );
  	else
  	  $AppUI->redirect( "m=dPpatients&tab=vw_idx_patients&id=$created&nom=&prenom=" );
  }
  else {
  	$textSiblings = "Risque de doublons :";
    foreach($siblings as $key => $value) {
      $textSiblings .= "\n>> ".$value["nom"]." ".$value["prenom"].
                       " né(e) le ".$value["naissance"].
                       " habitant ".$value["adresse"]." ".$value["CP"]." ".$value["ville"];
    }
    $textSiblings .= "\nVoulez-vous tout de même le créer ?";
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('patientSib', $patientSib);
$smarty->assign('patient', $patient);
$smarty->assign('created', $created);
$smarty->assign('textSiblings', $textSiblings);

$smarty->display('vw_edit_patients.tpl');
?>