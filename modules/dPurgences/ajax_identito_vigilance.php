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

// L'utilisateur doit-il voir les informations médicales

$sejour = new CSejour;
$where = array();
$where["sejour.entree_reelle"] = "LIKE '$date%'";
$where["sejour.type"] = "= 'urg'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "entree_reelle";

$guesses = array();
$sejours = $sejour->loadList($where, $order);
foreach ($sejours as &$_sejour) {
  $_sejour->loadRefRPU();

  // Chargement du numero de dossier
  $_sejour->loadNumDossier();

  // Chargement de l'IPP
  $_sejour->loadRefPatient();
	$patient =& $_sejour->_ref_patient;
  $patient->loadIPP();
	
  CSQLDataSource::$trace = true;
	$siblings = $patient->getSiblings();
	foreach ($siblings as $_sibling) {
		mbTrace("$_sibling->_view ($_sibling->_id)", "Sibling for $patient->_view");
	}
	
	$matching = $patient;
	$matching->loadMatchingPatient(true);
  CSQLDataSource::$trace = false;
  mbTrace("$matching->_view ($matching->_id)", "Subling for $patient->_view");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("sejours", $sejours);

$smarty->display("inc_identito_vigilance.tpl");
?>