<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can;

$prescription_line_medicament_id = mbGetValueFromPost("prescription_line_medicament_id");
$sejour_id = mbGetValueFromPost("sejour_id");
$user_id = mbGetValueFromPost("user_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement des prescriptions du sejour
$prescription = new CPrescription();
$sejour->loadRefsPrescriptions();

if(!$sejour->_ref_prescriptions["sejour"]->_id){
  // Si la prescription de pre-admission n'existe pas, on la crée
  if(!$sejour->_ref_prescriptions["pre_admission"]->_id){
    $prescription_preadm = new CPrescription();
	  $prescription_preadm->object_id = $sejour->_id;
	  $prescription_preadm->object_class = $sejour->_class_name;
	  $prescription_preadm->type = "pre_admission";
	  $msg = $prescription_preadm->store();
	  $AppUI->displayMsg($msg, "CPrescription-msg-create");
  }
  $prescription_sejour = new CPrescription();
  $prescription_sejour->object_id = $sejour->_id;
  $prescription_sejour->object_class = $sejour->_class_name;
  $prescription_sejour->type = "sejour";
  $msg = $prescription_sejour->store();
  
  $AppUI->displayMsg($msg, "CPrescription-msg-create");
} else {
  $prescription_sejour = $sejour->_ref_prescriptions["sejour"];
}


$line = new CPrescriptionLineMedicament();
$line->load($prescription_line_medicament_id);

$line->loadRefsPrises();

// Sauvegarde de la ligne de traitement personnel
$line->_id = "";
$line->traitement_personnel = 1;
$line->prescription_id = $prescription_sejour->_id;
$line->praticien_id = $can->admin ? $sejour->praticien_id : $user_id;
$line->debut = mbDate($sejour->_entree);
$msg = $line->store();
$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-create");

// Sauvegarde des prises
foreach($line->_ref_prises as $_prise){
  $_prise->_id = "";
  $_prise->object_id = $line->_id;
  $_prise->object_class = $line->_class_name;
  $msg = $_prise->store();
  $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
}

echo $AppUI->getMsg();
CApp::rip();

?>