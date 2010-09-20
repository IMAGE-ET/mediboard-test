<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_min = CValue::getOrSession("date_min", mbDate("- 1 WEEK"));
$date_max = CValue::getOrSession("date_max", mbDate());

$filter_sejour = new CSejour();
$filter_sejour->_date_entree = $date_min;
$filter_sejour->_date_sortie = $date_max;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter_sejour", $filter_sejour);
$smarty->display('vw_idx_stupefiant.tpl');

?>