<?php /* $Id: vw_files.php 6345 2009-05-22 14:10:55Z mytto $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: 6345 $
* @author Thomas Despoix
*/

global $can;
$can->needsAdmin();

$file = new CFile;
$ds = $file->_spec->ds;
$query = "SELECT COUNT(`file_id`) AS files_count, SUM(`file_size`) AS files_weight, `file_owner`, users.user_first_name, users.user_last_name   
  FROM `files_mediboard` 
  LEFT JOIN `users` ON `users`.`user_id` = `file_owner`
  GROUP BY `file_owner`
  ORDER BY `files_weight` DESC";
$stats_user = $ds->loadList($query);
$stats_func = array();

$total = array(
  "files_weight" => 0,
  "files_count" => 0,
);

// Stat per user
foreach ($stats_user as &$_stat_user) {
  $total["files_weight"] += $_stat_user["files_weight"];
  $total["files_count"]  += $_stat_user["files_count"];

  $_stat_user["_file_average_weight"] = $_stat_user["files_weight"] / $_stat_user["files_count"];
  $_stat_user["_file_average_weight"] = CMbString::toDecaBinary($_stat_user["_file_average_weight"]);
  $_stat_user["_files_weight"]        = CMbString::toDecaBinary($_stat_user["files_weight"]);

  // Make it mediusers uninstalled compliant
  if (CModule::getActive("mediusers")) {
    // Get the owner
    $user = new CMediusers();
    $user = $user->getCached($_stat_user["file_owner"]);
    $user->loadRefFunction();
    $_stat_user["_ref_owner"] = $user;

    // Initilize function data
    if (!isset($stats_func[$user->function_id])) {
      $stats_func[$user->function_id] = array(
        "files_weight" => 0,
        "files_count"  => 0,
      );
    }
    
    // Cummulate data per function
    $stat_func =& $stats_func[$user->function_id];
    $stat_func["files_weight"] += $_stat_user["files_weight"];
    $stat_func["files_count"]  += $_stat_user["files_count"];
  }
}

// Get user data percentages
foreach ($stats_user as &$_stat_user) {
  $_stat_user["_files_weight_percent"] = $_stat_user["files_weight"] / $total["files_weight"];
  $_stat_user["_files_count_percent" ] = $_stat_user["files_count"] / $total["files_count" ];
}

// Get function data percentages
foreach ($stats_func as $function_id => &$_stat_func) {
  $_stat_func["_files_weight_percent"] = $_stat_func["files_weight"] / $total["files_weight"];
  $_stat_func["_files_count_percent" ] = $_stat_func["files_count" ] / $total["files_count" ];
  $_stat_func["_file_average_weight"] = $_stat_func["files_weight"] / $_stat_func["files_count"];
  $_stat_func["_file_average_weight"] = CMbString::toDecaBinary($_stat_func["_file_average_weight"]);
  $_stat_func["_files_weight"] = CMbString::toDecaBinary($_stat_func["files_weight"]);

  // Get the owner
  $func = new CFunctions();
  $func = $func->getCached($function_id);
  $_stat_func["_ref_owner"] = $func;
}

$total["_file_average_weight"] = $total["files_count"] ? ($total["files_weight"] / $total["files_count"]) : 0;
$total["_file_average_weight"] = CMbString::toDecaBinary($total["_file_average_weight"]);
$total["_files_weight"]        = CMbString::toDecaBinary($total["files_weight"]);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("stats_user", $stats_user);
$smarty->assign("stats_func", $stats_func);
$smarty->assign("total", $total);
$smarty->display("vw_stats.tpl");

?>