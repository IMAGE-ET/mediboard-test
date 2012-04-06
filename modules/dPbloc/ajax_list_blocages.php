<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$blocage_id = CValue::getOrSession("blocage_id");
$date_replanif = CValue::getOrSession("date_replanif");

$date_min = mbTransformTime(null, $date_replanif, "%Y-%m-01");
$date_max = mbDate("-1 day", mbDate("+1 month", $date_min));

$bloc = new CBlocOperatoire;
$blocs = $bloc->loadListWithPerms(PERM_READ, null, "nom");

$blocages = array();
$salles   = array();

foreach ($blocs as $_bloc) {
  $salle = new CSalle;
  $where["bloc_id"] = "= '$_bloc->_id'";
  $salles[$_bloc->_id] = $_bloc->loadRefsSalles();
  
  foreach ($salles[$_bloc->_id] as $_salle) {
    $blocage = new CBlocage;
    $whereBloc = array();
    $whereBloc["salle_id"] = "= '$_salle->_id'";
    $whereBloc[] = "deb < '$date_max' AND fin > '$date_min'";
    
    $blocages[$_salle->_id] = $blocage->loadList($whereBloc);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("blocs"     , $blocs);
$smarty->assign("salles"    , $salles);
$smarty->assign("blocages"  , $blocages);
$smarty->assign("blocage_id", $blocage_id);
$smarty->assign("date_replanif", $date_replanif);
$smarty->assign("date_before", mbDate("-1 month", $date_replanif));
$smarty->assign("date_after" , mbDate("+1 month", $date_replanif));

$smarty->display("inc_list_blocages.tpl");

?>