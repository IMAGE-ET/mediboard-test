<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Séjours concernés
$date = CValue::get("date", mbDate());
$where["type"] = "= 'ssr'";
$sejours = CSejour::loadListForDate($date, $where);
foreach($sejours as $_sejour) {
	$_sejour->loadRefPatient();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_sejours_non_affectes.tpl");
?>