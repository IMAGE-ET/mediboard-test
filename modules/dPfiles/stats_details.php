<?php /* $Id: vw_files.php 6345 2009-05-22 14:10:55Z mytto $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: 6345 $
* @author Thomas Despoix
*/
 
CCanDo::checkAdmin();

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
$file = new CFile;
$ds = $file->_spec->ds;
$in_owner = $ds->prepareIn($user_ids);

// Query info
$query = "SELECT COUNT(`file_id`) AS `files_count`, SUM(`file_size`) AS `files_weight`, `object_class`, `file_category_id`
  FROM `files_mediboard` 
  WHERE `file_owner` $in_owner
  GROUP BY `object_class`, `file_category_id`";
$results = $ds->loadList($query);

// Reorder and make totals
$details = array();
$class_totals = array();
$category_totals = array();
$big_totals = array();
foreach ($results as $_result) {
  $files_count  = $_result["files_count"];
  $files_weight = $_result["files_weight"];
  $object_class = $_result["object_class"];
  $category_id  = $_result["file_category_id"];
  
  // Reorder
  $details[$category_id][$object_class] = array(
    "count"  => $files_count,
    "weight" => $files_weight,
  );
  
  // Totals
  $report = error_reporting(0);
  $class_totals[$object_class]["count" ] += $files_count;
  $class_totals[$object_class]["weight"] += $files_weight;
  $category_totals[$category_id]["count" ] += $files_count;
  $category_totals[$category_id]["weight"] += $files_weight;
  $big_totals["count" ] += $files_count;
  $big_totals["weight"] += $files_weight;
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
?>