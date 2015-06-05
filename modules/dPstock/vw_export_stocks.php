<?php 

/**
 * $Id$
 *  
 * @category Stock
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$category_id = CValue::get("category_id");

$category = new CProductCategory();
$category->load($category_id);

CStoredObject::$useObjectCache = false;

$backrefs_tree = array(
  "CProductCategory" => array(
    "products",
  ),
  "CProduct" => array(
    'references',
    'stocks_group',
    'stocks_service',
  ),
  "CProductReference" => array(
    // None
  ),
  "CProductStockGroup" => array(
    // None
  ),
  "CProductStockService" => array(
    // None
  ),
);

$fwdrefs_tree = array(
  "CProduct" => array(
    "category_id",
    "societe_id",
  ),
  "CProductReference" => array(
    "product_id",
    "societe_id",
  ),
  "CProductStockGroup" => array(
    "product_id",
    "group_id",
    "location_id",
  ),
  "CProductStockService" => array(
    "product_id",
    "object_id",
  ),
  "CProductStockLocation" => array(
    "group_id",
    "object_id",
  ),
);

$export = new CMbObjectExport($category, $backrefs_tree);
$export->empty_values = false;
$export->setForwardRefsTree($fwdrefs_tree);

$export->streamXML();
