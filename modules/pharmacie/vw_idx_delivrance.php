<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$delivrance = new CProductDelivery();

$date_min = CValue::getOrSession('_date_min', mbDate("-30 DAY"));
$date_max = CValue::getOrSession('_date_max', mbDate("+2 DAY"));

CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivrance',    $delivrance);

$smarty->display('vw_idx_delivrance.tpl');

?>