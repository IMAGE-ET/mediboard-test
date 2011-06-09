<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$where = array(
  "product_stock_group.quantity" => "> 0",
);

$stock = new CProductStockGroup;
$stocks = $stock->loadList($where);

$sum = 0;
foreach($stocks as $_stock) {
  $refs = $_stock->loadRefProduct(false)->loadBackRefs('references');
  
  if (count($refs) > 1) {
    //mbTrace(count($refs), $_stock->_view);
  }
  
  foreach($refs as $_ref) {
    $sum += $_ref->price * $_stock->quantity;
  }
}

$ref = new CProductReference;
$ref->price = $sum;

echo $ref->getFormattedValue("price");
