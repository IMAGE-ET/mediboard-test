<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["dPstock"] = array (
  "group_independent" => '0',
  "CProductOrder" => array (
    "order_number_format" => "%y%m%d%H%M%id",
    "order_number_contextual" => "0",
  ),
  "CProductStockGroup" => array (
    "infinite_quantity" => '0',
    "unit_order" => '0',
    "pagination_size" => 30
  ),
  "CProductStockService" => array (
    "infinite_quantity" => '0',
    "pagination_size" => 30
  ),
	"CProductReference" => array (
    "show_cond_price" => '1',
    "use_mdq" => '1',
    "pagination_size" => 15
	),
  "CProduct" => array (
    "pagination_size" => 15
  ),
);
