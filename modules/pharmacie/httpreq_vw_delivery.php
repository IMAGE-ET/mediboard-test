<?php /* $Id: httpreq_vw_deliveries_list.php 9028 2010-05-26 09:59:55Z phenxdesign $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 9028 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$delivery_id = CValue::get('delivery_id');

$delivery = new CProductDelivery();
$delivery = $delivery->load($delivery_id);
$delivery->loadRefsFwd();
$delivery->loadRefsBack();
$delivery->_ref_stock->loadRefsFwd();
$delivery->isDelivered();

$stocks_service = array();
$stocks_service[$delivery->_id] = CProductStockService::getFromCode($delivery->_ref_stock->_ref_product->code, $delivery->service_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('curr_delivery',  $delivery);
$smarty->assign('stocks_service', $stocks_service);
$smarty->assign('line_refresh', true);

$smarty->display('inc_vw_line_delivrance.tpl');
