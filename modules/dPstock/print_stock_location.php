<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$stock_location_id = CValue::get('stock_location_id');
$empty             = CValue::get('empty');

$stock_location = new CProductStockLocation();
$stock_location->load($stock_location_id);
$stock_location->loadRefsStocks();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('stock_location', $stock_location);
$smarty->assign('empty', $empty);
$smarty->display('print_stock_location.tpl');

