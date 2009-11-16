<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = CValue::get("prescription_id");
$praticien_id    = CValue::get("praticien_id", $AppUI->user_id);
$date            = CValue::get("date", mbDate());
$time            = CValue::get("time_debut");
$actionType      = CValue::get("actionType", "stop");
$mode_pharma     = CValue::get("mode_pharma");

// Chargement des traitements perso
$traitement_perso = new CPrescriptionLineMedicament();
$traitement_perso->prescription_id = $prescription_id;
$traitement_perso->traitement_personnel = "1";
$traitements = $traitement_perso->loadMatchingList();

foreach($traitements as &$line) {
	if($actionType == "stop" && !$line->date_arret && $line->signee) {
		$line->date_arret = $date;
		$line->time_arret = $time;
    CAppUI::displayMsg($line->store(), "CPrescriptionLineMedicament-msg-store");
	}
	if($actionType == "go" && $line->date_arret) {
		$line->duplicateLine($praticien_id, $prescription_id, $date, $time);
	}
}

echo "<script type='text/javascript'>Prescription.reload($prescription_id, '', 'medicament', '0', $mode_pharma)</script>";
echo CAppUI::getMsg();
CApp::rip();
?>