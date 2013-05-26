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

// Loads the expected Order
$reception = new CProductReception();
$reception->load($reception_id);
$reception->loadRefsBack();

foreach ($reception->_ref_reception_items as $_reception) {
  $_reception->loadRefOrderItem()->loadReference();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('reception', $reception);
$smarty->display('inc_reception.tpl');
