<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Plateaux disponibles
$technicien_id = CValue::get("technicien_id");
$date = CValue::getOrSession("date", mbDate());

$technicien = new CTechnicien();
$technicien->load($technicien_id);
$technicien->loadRefKine();
$kine_id = $technicien->_ref_kine->_id;

$sejours = CBilanSSR::loadSejoursSSRfor($technicien_id, $date);
foreach ($sejours as $_sejour) {
  $_sejour->checkDaysRelative($date);
  $_sejour->loadRefPatient();
}

// Remplacements
$plage = new CPlageConge;
$sejours_remplaces = array();
$remplacements = $plage->loadRefsReplacementsFor($kine_id, $date);
foreach ($remplacements as $_remplacement) {
  $_remplacement->loadRefUser();
	$_remplacement->_refs_sejours_remplaces = CBilanSSR::loadSejoursSSRfor($_remplacement->user_id, $date);
	
	// Détails des séjours remplacés
	foreach ($_remplacement->_refs_sejours_remplaces as $_sejour) {
	  $_sejour->checkDaysRelative($date);
	  $_sejour->loadRefPatient();
	}
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("remplacements", $remplacements);
$smarty->display("inc_sejours_technicien.tpl");
?>