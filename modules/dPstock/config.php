<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["dPstock"] = array (
  "group_independent" => false,
  "CProductOrder" => array(
    "order_number_format" => "%y%m%d%H%M%id",
  ),
  "CProductStockGroup" => array(
    "infinite_quantity" => false,
    "unit_order" => false,
  ),
  "CProductStockService" => array(
    "infinite_quantity" => false,
  )
);