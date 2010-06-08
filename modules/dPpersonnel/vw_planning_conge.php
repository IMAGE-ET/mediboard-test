<?php /* $Id: index.php 7320 2009-11-14 22:42:28Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
//$can->needsRead();
$choix = CValue::get("choix", "mois");
$affiche_nom = CValue::get("affiche_nom",1); 
$filter = new CPlageConge();
$filter->user_id = CValue::get("user_id", CAppUI::$user->_id);
$filter->date_debut = CValue::get("date_debut",mbDate());

$mediuser  = new CMediusers();
$mediusers = $mediuser->loadListFromType();

if(!$filter->date_debut) {
	$filter->date_debut = Date("Y-m-d");
}
// Si la date rentrée par l'utilisateur est un lundi,
// on calcule le dimanche d'avant et on rajoute un jour. 

$tab_start = array();
if($choix=="semaine"){
$last_sunday = mbTransformTime('last sunday',$filter->date_debut,'%Y-%m-%d');
$last_monday = mbTransformTime('+1 day',$last_sunday,'%Y-%m-%d');
$debut_periode = $last_monday;

$fin_periode = mbTransformTime('+6 day',$debut_periode,'%Y-%m-%d');
}
else if($choix=="annee") {
list($year,$m,$j)=explode("-",$filter->date_debut);
$debut_periode = "$year-01-01";
$fin_periode = "$year-12-31";
 $j=1;
for ($i=1;$i<13;$i++){
	if (!date("w", mktime(0,0,0,$i,1,$year))) {
	  $tab_start[$j] = 7;
	} else {
	$tab_start[$j]= date("w", mktime(0,0,0,$i,1,$year));
	}
	$j++;
	$tab_start[$j]= date("t", mktime(0,0,0,$i,1,$year));
	$j++;
}


}
else {
list($a,$m,$j)=explode("-",$filter->date_debut);
$debut_periode  = "$a-$m-01";
$fin_periode = mbTransformTime('+1 month',$debut_periode,'%Y-%m-%d');
$fin_periode  = mbTransformTime('-1 day', $fin_periode,'%Y-%m-%d');

}
$tableau_periode = array();

for($i = 0 ; $i < mbDaysRelative($debut_periode,$fin_periode) + 1; $i ++) {
	$tableau_periode[$i] = mbTransformTime('+'.$i.'day',$debut_periode,'%Y-%m-%d');
}


$where = array();
$where[] = "((date_debut >= '$debut_periode' AND date_debut <= '$fin_periode'" .
         ")OR (date_fin >= '$debut_periode' AND date_fin <= '$fin_periode')".
				 "OR (date_debut <='$debut_periode' AND date_fin >= '$fin_periode'))";
$where["user_id"] = CSQLDataSource::prepareIn(array_keys($mediusers), $filter->user_id);

$plageconge = new CPlageConge();
$plagesconge = array();
$orderby="user_id";
$plagesconge = $plageconge->loadList($where, $orderby);
$tabUser_plage = array();
$tabUser_plage_indices = array();
foreach ($plagesconge as $_plage) {
  $_plage->loadRefsFwd();
  $_plage->_ref_user->loadRefFunction();
	$_plage->_deb = mbDaysRelative($debut_periode,$_plage->date_debut);
	$_plage->_fin = mbDaysRelative($_plage->date_debut, $_plage->date_fin)+1;
	$_plage->_duree = mbDaysRelative($_plage->date_debut,$_plage->date_fin)+1;
}

$smarty = new CSmartyDP();

$smarty->assign("debut_periode",   $debut_periode);
$smarty->assign("filter",          $filter);
$smarty->assign("plagesconge",     $plagesconge);
$smarty->assign("choix",           $choix);
$smarty->assign("mediusers",       $mediusers);
$smarty->assign("tableau_periode", $tableau_periode);
$smarty->assign("affiche_nom",     $affiche_nom);
$smarty->assign("tab_start",       $tab_start);

$smarty->display("vw_planning_conge.tpl");

?>