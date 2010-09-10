<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$num_days_date_min = CAppUI::conf("pharmacie num_days_date_min");
$date_min = CValue::get('_date_min', mbDate("-$num_days_date_min DAY"));
$date_max = CValue::get('_date_max', mbDate("+2 DAY"));
$start = CValue::getOrSession('start', 0);

$delivrance = new CProductDelivery();
$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;
$delivrance->quantity = 1;
$delivrance->date_delivery = mbDateTime();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('delivrance',    $delivrance);
$smarty->assign('start', $start);

$smarty->display('vw_idx_outflow.tpl');

?>