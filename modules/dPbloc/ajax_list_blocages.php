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
$date_replanif = CValue::getOrSession("date_replanif", CMbDT::date());

$date_min = CMbDT::transform(null, $date_replanif, "%Y-%m-01");
$date_max = CMbDT::date("-1 day", CMbDT::date("+1 month", $date_min));

$bloc = new CBlocOperatoire();
$where = array("group_id" => "= '".CGroups::loadCurrent()->_id."'");
$blocs = $bloc->loadListWithPerms(PERM_READ, $where, "nom");

$blocages = array();
$salles   = array();

foreach ($blocs as $_bloc) {
  $salles[$_bloc->_id] = $_bloc->loadRefsSalles();
  
  foreach ($salles[$_bloc->_id] as $_salle) {
    $blocage = new CBlocage();
    $whereBloc = array();
    $whereBloc["salle_id"] = "= '$_salle->_id'";
    $whereBloc[] = "deb <= '$date_max' AND fin >= '$date_min'";
    
    $blocages[$_salle->_id] = $blocage->loadList($whereBloc);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("blocs"     , $blocs);
$smarty->assign("salles"    , $salles);
$smarty->assign("blocages"  , $blocages);
$smarty->assign("blocage_id", $blocage_id);
$smarty->assign("date_replanif", $date_replanif);
$smarty->assign("date_before", CMbDT::date("-1 month", $date_replanif));
$smarty->assign("date_after" , CMbDT::date("+1 month", $date_replanif));

$smarty->display("inc_list_blocages.tpl");

?>