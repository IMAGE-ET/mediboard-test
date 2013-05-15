<?php

/**
 * dPbloc
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$salle_id = CValue::get("salle_id");
$deb      = CValue::get("deb");
$fin      = CValue::get("fin");

if ($deb > $fin) {
  list($deb, $fin) = array($fin, $deb);
}

$plage = new CPlageOp;

$where = array();

$where["salle_id"] = "= '$salle_id'";
$where["date"]     = "BETWEEN '$deb' AND '$fin'";

$plages = $plage->loadList($where);

foreach ($plages as $_key => $_plage) {
  if ($_plage->countBackRefs("operations") > 0) {
    unset($plages[$_key]);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("plages", $plages);

$smarty->display("inc_list_plages_to_delete.tpl");
