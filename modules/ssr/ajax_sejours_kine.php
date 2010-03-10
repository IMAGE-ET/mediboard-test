<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

// Plateaux disponibles
$kine_id = CValue::get("kine_id");
$date = CValue::getOrSession("date", mbDate());
$sejours = CBilanSSR::loadSejoursSSRfor($kine_id, $date);
foreach($sejours as $_sejour) {
  $_sejour->checkDaysRelative($date);
  $_sejour->loadRefPatient();
  $_sejour->loadRefBilanSSR();
	$_sejour->loadRefPrescriptionSejour();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_sejours_kine.tpl");
?>