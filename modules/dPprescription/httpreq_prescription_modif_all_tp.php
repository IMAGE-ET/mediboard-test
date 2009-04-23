<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = mbGetValueFromGet("prescription_id");
$praticien_id    = mbGetValueFromGet("praticien_id", $AppUI->user_id);
$date            = mbGetValueFromGet("date", mbDate());
$time            = mbGetValueFromGet("time_debut");
$actionType      = mbGetValueFromGet("actionType", "stop");
$mode_pharma     = mbGetValueFromGet("mode_pharma");

// Chargement des traitements perso
$traitement_perso = new CPrescriptionLineMedicament();
$traitement_perso->prescription_id = $prescription_id;
$traitement_perso->traitement_personnel = "1";
$traitements = $traitement_perso->loadMatchingList();

foreach($traitements as &$line) {
	if($actionType == "stop" && !$line->date_arret && $line->signee) {
		$line->date_arret = $date;
		$line->time_arret = $time;
    $AppUI->displayMsg($line->store(), "CPrescriptionLineMedicament-msg-store");
	}
	if($actionType == "go" && $line->date_arret) {
		$line->duplicateLine($praticien_id, $prescription_id, $date, $time);
	}
}

echo "<script type='text/javascript'>Prescription.reload($prescription_id, '', 'medicament', '0', $mode_pharma)</script>";
echo $AppUI->getMsg();
CApp::rip();
?>