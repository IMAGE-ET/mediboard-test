<?php /* $Id: index.php 7320 2009-11-14 22:42:28Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
$choix = CValue::getOrSession("choix", "mois");
$filter = new CPlageVacances();
$filter->date_debut = CValue::getOrSession("date_debut");



if(!$filter->date_debut) {
	$filter->date_debut = Date("Y-m-d");
}
// Si la date rentrée par l'utilisateur est un lundi,
// on calcule le dimanche d'avant et on rajoute un jour. 

if($choix=="semaine"){
$last_sunday = mbTransformTime('last sunday',$filter->date_debut,'%Y-%m-%d');
$last_monday = mbTransformTime('+1 day',$last_sunday,'%Y-%m-%d');
$debut_periode = $last_monday;

$fin_periode = mbTransformTime('+6 day',$debut_periode,'%Y-%m-%d');
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
$where = "(date_debut >= '$debut_periode' AND date_debut <= '$fin_periode'" .
         ")OR (date_fin >= '$debut_periode' AND date_fin <= '$fin_periode')".
				 "OR (date_debut <='$debut_periode' AND date_fin >= '$fin_periode')";
$plagevac = new CPlageVacances();
$plagesvac = array();
$orderby="user_id";
$plagesvac = $plagevac->loadList($where, $orderby);
$tabUser_plage = array();
$tabUser_plage_indices = array();

foreach ($plagesvac as $_plage) {
  $_plage->loadRefsFwd();
  $_plage->_ref_user->loadRefFunction();
  
	$_plage->_deb = mbDaysRelative($debut_periode,$_plage->date_debut);
	$_plage->_fin = mbDaysRelative($_plage->date_debut, $_plage->date_fin)+1;
	$_plage->_duree = mbDaysRelative($_plage->date_debut,$_plage->date_fin)+1;
}

$smarty = new CSmartyDP();

$smarty->assign("debut_periode",   $debut_periode);
$smarty->assign("filter",          $filter);
$smarty->assign("plagesvac",       $plagesvac);
$smarty->assign("choix",       $choix);
$smarty->assign("tableau_periode", $tableau_periode);

$smarty->display("vw_planning_vacances.tpl");