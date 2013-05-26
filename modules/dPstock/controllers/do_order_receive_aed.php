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

$callback     = CValue::post('callback');
$_order_items = CValue::post('_order_items');
  
if ($_order_items) {
  $_order_items = stripslashes($_order_items);
  $_order_items = json_decode($_order_items, true);
  
  foreach ($_order_items as $_i => $_data) {
    $do = new CDoObjectAddEdit('CProductOrderItemReception');
    unset($do->request); // breaks the reference, don't remove this line !
    $do->request = $_data;
    $do->redirect = null;
    $do->callBack = null;
    $do->doIt();
  }
  
  if ($callback) {
    echo "<script type=\"text/javascript\">$callback()</script>";
  }
}

echo CAppUI::getMsg();
CApp::rip();
