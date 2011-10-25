<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

$dossier_medical_id = CValue::post("dossier_medical_id");
$prescription_id    = CValue::post("prescription_id");
$jour_decalage      = CValue::post("jour_decalage");
$decalage_line      = CValue::post("decalage_line");
$unite_decalage     = CValue::post("unite_decalage");
$operation_id       = CValue::post("operation_id");
$debut              = CValue::post("debut");
$time_debut         = CValue::post("time_debut");
$praticien_id       = CValue::post("praticien_id");

$dossier_medical = new CDossierMedical;
$dossier_medical->load($dossier_medical_id);

// Chargement de la prescription du dossier medical
$prescription_tp = $dossier_medical->loadUniqueBackRef("prescription");   
$prescription_tp->loadRefsLinesMedComments();
 
if(count($prescription_tp->_ref_lines_med_comments["med"])){
	foreach($prescription_tp->_ref_lines_med_comments["med"] as $_line) {
		$_line->loadRefsPrises();
		
		// praticien_id, debut, time_debut, jour_decalage, decalage_line, unite_decalage, operation_id
		// Sauvegarde de la ligne de traitement personnel
		$_line->_id = "";
		$_line->traitement_personnel = 1;
		$_line->prescription_id = $prescription_id;
		$_line->praticien_id = $praticien_id;
		$_line->creator_id = $user->_id;
		$_line->debut = $debut;
		$_line->time_debut = $time_debut;
		$_line->jour_decalage = $jour_decalage;
    $_line->decalage_line = $decalage_line;
    $_line->unite_decalage = $unite_decalage;
    $_line->operation_id = $operation_id;
		$_line->fin = "";

		$msg = $_line->store();
		CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
		
		// Sauvegarde des prises
		foreach($_line->_ref_prises as $_prise){
		  $_prise->_id = "";
		  $_prise->object_id = $_line->_id;
		  $_prise->object_class = $_line->_class;
		  $msg = $_prise->store();
		  CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
		}
	}
}

CAppUI::callbackAjax("selectLines", $prescription_id, null, null, 1);
CApp::rip();

?>