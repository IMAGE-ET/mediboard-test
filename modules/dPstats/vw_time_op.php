<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

date_default_timezone_set("UTC");

global $AppUI, $can, $m;

$can->needsEdit();

$typeVue        = CValue::getOrSession("typeVue"       , 2);
$nb_sejour_mini = CValue::getOrSession("nb_sejour_mini", 3);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

// Stat des temps de préparation
if ($typeVue == 0){
  include("inc_vw_timeop_op.php");
} 
// Stat des temps opératoires
elseif($typeVue == 1) {
  include("inc_vw_timeop_prepa.php");
} 
// Stat des temps d'hospitalisation
else {
  include("inc_vw_timehospi.php");
}


// Création du template
$smarty = new CSmartyDP();

if ($typeVue == 0 || $typeVue == 2) {
  $smarty->assign("prat_id"  , $prat_id  );
  $smarty->assign("codeCCAM" , $codeCCAM );
  $smarty->assign("listPrats", $listPrats);
}

if ($typeVue == 2) {
  $sejour = new CSejour;
  $listHospis = $sejour->_specs["type"]->_locales;
  unset($listHospis["exte"]);
  $smarty->assign("listHospis", $listHospis);
  $smarty->assign("type"      , $type);
}

$smarty->assign("user_id"       , $AppUI->user_id);
$smarty->assign("listTemps"     , $listTemps);
$smarty->assign("total"         , $total);
$smarty->assign("typeVue"       , $typeVue);
$smarty->assign("nb_sejour_mini", $nb_sejour_mini);

$smarty->display("vw_time_op.tpl");

?>