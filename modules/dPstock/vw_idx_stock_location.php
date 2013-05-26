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

CCanDo::checkEdit();

$stock_location = new CProductStockLocation;
$list_locations = $stock_location->loadList(null, 'object_class, object_id, position,name');

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list_locations', $list_locations);
$smarty->display('vw_idx_stock_location.tpl');

