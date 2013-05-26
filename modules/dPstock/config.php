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

$dPconfig["dPstock"] = array (
  "host_group_id" => '',
  "CProductOrder" => array (
    "order_number_format" => "%y%m%d%H%M%id",
    "order_number_contextual" => "0",
  ),
  "CProductStock" => array (
    "advanced_bargraph" => '0',
    "hide_bargraph" => '0',
    "allow_quantity_fractions" => '0',
  ),
  "CProductStockGroup" => array (
    "infinite_quantity" => '0',
    "pagination_size" => 30,
    "negative_allowed" => '1',
  ),
  "CProductStockService" => array (
    "infinite_quantity" => '0',
    "pagination_size" => 30,
  ),
  "CProductReference" => array (
    "show_cond_price" => '1',
    "use_mdq" => '1',
    "pagination_size" => 15,
  ),
  "CProduct" => array (
    "pagination_size" => 15,
    "use_renewable"   => 1,
    //"allow_same_name" => '0',
  ),
  "CProductDelivery" => array (
    "auto_dispensation" => 0,
  ),
);
