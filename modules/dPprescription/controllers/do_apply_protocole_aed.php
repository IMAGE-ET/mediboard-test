<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can;

$can->needsRead();

$prescription_id   = CValue::post("prescription_id");
$pack_protocole_id = CValue::post("pack_protocole_id");

$date_sel        = CValue::post("debut", mbDate());
$praticien_id    = CValue::post("praticien_id", $AppUI->user_id);
$operation_id    = CValue::post("operation_id");
$pratSel_id      = CValue::post("pratSel_id");


// Si aucun pack/protocole selectionne, on ne fait rien
if (!$pack_protocole_id){
  CAppUI::setMsg("Aucun protocole n'a été sélectionné", UI_MSG_ERROR);
  echo CAppUI::getMsg();
  CApp::rip();
}

// Si l'utilisateur est une infirmiere, on verifie si le protocole ne contient pas des lignes non prescriptibles
$current_user = new CMediusers();
$current_user->load($AppUI->user_id);
if($current_user->isInfirmiere() && !CModule::getCanDo("dPprescription")->admin){
	$count = array();
	
  $pack_protocole = explode("-", $pack_protocole_id);
  $pack_id = ($pack_protocole[0] === "pack") ? $pack_protocole[1] : "";
  $protocole_id = ($pack_protocole[0] === "prot") ? $pack_protocole[1] : "";

  if($pack_id){
  	$_pack = new CPrescriptionProtocolePack();
		$_pack->load($pack_id);
		$_pack->countElementsByChapitre();
    $count = $_pack->_counts_by_chapitre;
	}
	if($protocole_id){
		$_prot = new CPrescription();
		$_prot->load($protocole_id);
	  $_prot->countLinesMedsElements();
	  foreach($_prot->_counts_by_chapitre as $chapitre => $_count_chapitre){
	    if(!$_count_chapitre){
	      unset($_prot->_counts_by_chapitre[$chapitre]);
	    }
	  }
		$count = $_prot->_counts_by_chapitre; 
	}
	
	// Parcours des chapitres non vides
	$errors = array();
	foreach($count as $chapitre => $count_by_chap){
		if(!CAppUI::conf("dPprescription CPrescription droits_infirmiers_$chapitre")){
			$errors[] = $chapitre;
		}
	}
	
	if(count($errors)){
		CAppUI::setMsg("Impossible d'appliquer le protocole sélectionné car le compte infirmier ne permet pas de créer des lignes dans les chapitres suivants: ".join(", ", $errors), UI_MSG_ERROR);
    echo CAppUI::getMsg();
    CApp::rip();
	}
}

// Chargement de la prescription
$prescription = new CPrescription();
if ($prescription_id) {
  $prescription->load($prescription_id);
} else {
  $operation = new COperation();
  $operation->load($operation_id);
  $prescription->object_class = 'CSejour';
  $prescription->object_id = $operation->sejour_id;
  $prescription->type = 'sejour';
  if ($msg = $prescription->store()) {
	  CAppUI::setMsg($msg, UI_MSG_ERROR);
	}
}

// On applique le protocole ou le pack
$prescription->applyPackOrProtocole($pack_protocole_id, $praticien_id, $date_sel, $operation_id);

$lite = CAppUI::pref('mode_readonly') ? 0 : 1;

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id, null, null, null, null, null, null, true, $lite, null, '$pratSel_id', null, '$praticien_id')</script>";
echo CAppUI::getMsg();
CApp::rip();

?>