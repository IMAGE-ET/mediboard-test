<?php /* $Id:  $ */

/**
 *  @package Mediboard
 *  @subpackage dPprescription
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_mix_id = CValue::post("prescription_line_mix_id");
$planification_systeme_id = CValue::post("planification_systeme_id");
$datetime                 = CValue::post("datetime");
$original_datetime        = CValue::post("original_datetime");

// Chargement de la planif systeme
$planif = new CPlanificationSysteme();
$planif->load($planification_systeme_id);

// Chargement de la ligne
$prescription_line_mix = new CPrescriptionLineMix();
$prescription_line_mix->load($prescription_line_mix_id);
$prescription_line_mix->loadRefsLines();

// Planifications à partir des planifs systemes
if($planif->_id){
  // Creation des planifications
	foreach($prescription_line_mix->_ref_lines as $_perf_line){
		$_perf_line->updateQuantiteAdministration();
		
	  $planification = new CAdministration();
	  $planification->setObject($_perf_line);
		$planification->planification = 1;
		$planification->unite_prise = $_perf_line->_unite_administration;
		$planification->quantite = $_perf_line->_quantite_administration;
		$planification->administrateur_id = CAppUI::$user->_id;
		$planification->dateTime = $datetime;
	  $planification->original_dateTime = $planif->dateTime;
	  $planification->store();
	}
} 
// Replanification
else {
	// Chargement des planifications deja realisées
	foreach($prescription_line_mix->_ref_lines as $_perf_line){
	  $planification = new CAdministration();
  	$planification->setObject($_perf_line);
		$planification->planification = 1;
		$planification->dateTime = $original_datetime;
		$planification->loadMatchingObject();
		if($planification->_id){
			$planification->dateTime = $datetime;
			$planification->store();
		}
	}
}

CApp::rip();

?>