<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Chargement de la liste des kines
$mediuser = new CMediusers();
$kines = $mediuser->loadKines();

$date = CValue::get("date", mbDate());

// Parcours des kines et chargement du planning
foreach($kines as $_kine){
	$args_planning = array();
  $args_planning["kine_id"] = $_kine->_id;
  $args_planning["surveillance"] = 0;
  $args_planning["large"] = 1;
  $args_planning["print"] = 1;
	$args_planning["height"] = 600;
  $args_planning["date"] = $date;
  
	// Chargement du planning de technicien
  $plannings[$_kine->_id]["technicien"] = CApp::fetch("ssr", "ajax_planning_technicien", $args_planning);	
	
	// Chargement du planning de surveillance
	$args_planning["surveillance"] = 1;
  
	$plannings[$_kine->_id]["surveillance"] = CApp::fetch("ssr", "ajax_planning_technicien", $args_planning); 	
}

$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));
		
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plannings", $plannings);
$smarty->assign("kines", $kines);
$smarty->assign("date", $date);
$smarty->assign("monday", $monday);
$smarty->assign("sunday", $sunday);
$smarty->display("offline_plannings_techniciens.tpl");

?>