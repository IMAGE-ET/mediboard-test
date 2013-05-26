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

$reception_id = CValue::get('reception_id');

$reception = new CProductReception();
$reception->load($reception_id);
$reception->loadRefsBack();
$reception->loadRefsFwd();
$reception->updateTotal();

foreach ($reception->_ref_reception_items as $_reception_item) {
  $_reception_item->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("reception", $reception);
$smarty->display('print_reception.tpl');

