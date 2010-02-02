<?php /* $Id: httpreq_vw_main_courante.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;
$can->needsEdit();

// Selection de la date
$date = CValue::getOrSession("date", mbDate());

// Chargement des séjours concernés
$sejour = new CSejour;
$where = array();
$where["sejour.entree_reelle"] = "LIKE '$date%'";
$where["sejour.type"] = "= 'urg'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "entree_reelle";
$sejours = $sejour->loadList($where, $order);

$guesses = array();
$patients = array();
foreach ($sejours as &$_sejour) {
  $_sejour->loadRefRPU();

  // Chargement du numero de dossier
  $_sejour->loadNumDossier();

  // Chargement de l'IPP
  $_sejour->loadRefPatient();
	
	// Classement par patient
	if (!isset($patients[$_sejour->patient_id])) {
		$patients[$_sejour->patient_id] = $_sejour->_ref_patient;
	}
	
	$patients[$_sejour->patient_id]->_ref_sejours[$_sejour->_id] = $_sejour;
}

// Chargement des détails sur les patients
foreach ($patients as $patient) {
  $patient->loadIPP();
	
	$guess = array();
  $nicer = array();

	$siblings = $patient->getSiblings();
  $guess["siblings"] = array_keys($siblings);
  $nicer["siblings"] = CMbArray::pluck($siblings, "_view");
	
  $phonings = $patient->getPhoning($_sejour->_entree);
  $guess["phonings"] = array_keys($phonings);
  $nicer["phonings"] = CMbArray::pluck($phonings, "_view");
	
//	mbTrace($nicer, "Nicer for $patient->_view");
	
	$guesses[$patient->_id] = $guess;
}
mbTrace(CMbArray::pluck($patients, "_id"));


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("patients", $patients );

$smarty->display("inc_identito_vigilance.tpl");
?>