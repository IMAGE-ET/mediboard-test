<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsAdmin();

$user = new CUser();

// Find duplicates
$query = "SELECT `user_username`, COUNT(*) AS `user_count`
	FROM `users` 
	GROUP BY `user_username` 
	ORDER BY `user_count` DESC";
$ds= $user->_spec->ds;
$user_counts = $ds->loadHashList($query);
$siblings = array();

foreach ($user_counts as $user_name => $user_count) {
  // Only duplicates
  if ($user_count == 1) {
    break;
  }
  	
	$user->user_username = $user_name;
	$siblings[$user_name] = $user->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("siblings", $siblings);

$smarty->display("check_siblings.tpl");
?>