<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$prescription_line_medicament_id = CValue::post("prescription_line_medicament_id");
$prescription_id = CValue::post("prescription_id");
$jour_decalage = CValue::post("jour_decalage");
$decalage_line = CValue::post("decalage_line");
$unite_decalage = CValue::post("unite_decalage");
$operation_id = CValue::post("operation_id");
$debut = CValue::post("debut");
$time_debut = CValue::post("time_debut");
$praticien_id = CValue::post("praticien_id");

$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement de la ligne
$line = new CPrescriptionLineMedicament();
$line->load($prescription_line_medicament_id);
$line->loadRefsPrises();

// Sauvegarde de la ligne de traitement personnel
$line->_id = "";
$line->traitement_personnel = 1;
$line->prescription_id = $prescription->_id;
$line->praticien_id = $praticien_id;
$line->creator_id = $AppUI->user_id;
$line->debut = $debut;
$line->time_debut = $time_debut;
$line->jour_decalage = $jour_decalage;
$line->decalage_line = $decalage_line;
$line->unite_decalage = $unite_decalage;
$line->operation_id = $operation_id;
$line->fin = "";

$msg = $line->store();
CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");

// Sauvegarde des prises
foreach($line->_ref_prises as $_prise){
  $_prise->_id = "";
  $_prise->object_id = $line->_id;
  $_prise->object_class = $line->_class_name;
  $msg = $_prise->store();
  CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
}

echo CAppUI::getMsg();
CApp::rip();

?>