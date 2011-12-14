<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

CCanDo::checkAdmin();

$listHours = range(0, 23);
foreach($listHours as &$_hour){
	$_hour = str_pad($_hour,2,"0",STR_PAD_LEFT);
}

$hours_prolongation_time = range(0, 48);
foreach($hours_prolongation_time as &$_hour){
  $_hour = str_pad($_hour,2,"0",STR_PAD_LEFT);
}

// Chargement des mediusers
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

// Chargement des fonctions
$function = new CFunctions();
$functions = $function->loadSpecialites();

// Chargement des etablissements
$group = new CGroups();
$groups = $group->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listHours" , $listHours);
$smarty->assign("hours_prolongation_time", $hours_prolongation_time);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("functions" , $functions);
$smarty->assign("groups"    , $groups);
$smarty->display("configure.tpl");

?>