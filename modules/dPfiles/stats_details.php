<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Get concrete class
$doc_class = CValue::get("doc_class", "CFile");
if (!is_subclass_of($doc_class, "CDocumentItem")) {
  trigger_error("Wrong '$doc_class' won't inerit from CDocumentItem", E_USER_ERROR);
  return;
}

$owner = mbGetObjectFromGet(null, null, "owner_guid");

// Users
$user_ids = array();
if ($owner instanceof CFunctions) {
  $user_ids = array_keys($owner->loadBackRefs("users"));
}

if ($owner instanceof CUser || $owner instanceof CMediusers) {
  $user_ids = array($owner->_id);
}

// Query prepare
/** @var CDocumentItem $doc */
$doc = new $doc_class;
$results = $doc->getUsersStatsDetails($user_ids);

// Reorder and make totals
$details = array();
$class_totals = array();
$category_totals = array();
$big_totals = array(
  "count"  => 0,
  "weight" => 0,
);

foreach ($results as $_result) {
  $docs_count  = $_result["docs_count"];
  $docs_weight = $_result["docs_weight"];
  $object_class = $_result["object_class"];
  $category_id  = $_result["category_id"];
  
  // Reorder
  $details[$category_id][$object_class] = array(
    "count"  => $docs_count,
    "weight" => $docs_weight,
  );
  
  // Totals
  $report = error_reporting(0);
  $class_totals[$object_class]["count" ] += $docs_count;
  $class_totals[$object_class]["weight"] += $docs_weight;
  $category_totals[$category_id]["count" ] += $docs_count;
  $category_totals[$category_id]["weight"] += $docs_weight;
  $big_totals["count" ] += $docs_count;
  $big_totals["weight"] += $docs_weight;
  error_reporting($report);
}

// All categories
$category = new CFilesCategory();
$categories = $category->loadAll(array_keys($category_totals));

// All classes
$classes = array_keys($class_totals);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("details", $details);
$smarty->assign("class_totals", $class_totals);
$smarty->assign("category_totals", $category_totals);
$smarty->assign("big_totals", $big_totals);
$smarty->assign("categories", $categories);
$smarty->assign("classes", $classes);
$smarty->display("stats_details.tpl");
