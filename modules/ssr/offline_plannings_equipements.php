<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$plateau_tech = new CPlateauTechnique();
$plateau_tech->group_id = CGroups::loadCurrent()->_id;
$plateaux = $plateau_tech->loadMatchingList();
foreach($plateaux as $_plateau_tech){
  $_plateau_tech->loadRefsEquipements();
}

$date = mbDate();

foreach($plateaux as $_plateau){
	$_plateau->loadRefsEquipements();
	
	foreach($_plateau->_ref_equipements as $_equipement){
		if(!$_equipement->visualisable){
		  unset($_plateau->_ref_equipements[$_equipement->_id]);
			continue;
		}
		$equipements[$_equipement->_id] = $_equipement;
	  $args_planning = array();
	  $args_planning["equipement_id"] = $_equipement->_id;
	  $args_planning["date"] = $date;
	  $plannings[$_equipement->_id] = CApp::fetch("ssr", "ajax_planning_equipement", $args_planning);
	}
}
$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));
    
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plannings", $plannings);
$smarty->assign("plateaux", $plateaux);
$smarty->assign("equipements", $equipements);
$smarty->assign("date", $date);
$smarty->assign("monday", $monday);
$smarty->assign("sunday", $sunday);
$smarty->display("offline_plannings_equipements.tpl");

?>