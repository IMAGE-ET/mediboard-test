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
$stats = $ds->loadList($query);
foreach($stats as &$_stat) {
	$_stat["_file_average_weight"] = mbConvertDecaBinary($_stat["files_weight"] / $_stat["files_count"]);
  $_stat["_files_weight"] = mbConvertDecaBinary($_stat["files_weight"]);
	if (CModule::getActive("mediusers")) {
		$user = new CMediusers;
		$user->load($_stat["file_owner"]);
		$user->loadRefFunction();
		$_stat["_ref_user"] = $user;
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("stats", $stats);
$smarty->display("vw_stats.tpl");

?>