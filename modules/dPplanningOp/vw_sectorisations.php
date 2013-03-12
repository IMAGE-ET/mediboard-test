<?php

/**
 * Add, edit, remove sectorisations rules
 *
 * @category DPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 CCanDo::checkAdmin();

$regleSector = new CRegleSectorisation();
$regleSector->group_id = CGroups::loadCurrent()->_id;

$showinactive = CValue::getOrSession("inactive", 0);

$where = array();
if (!$showinactive) {
  $where["date_max"] = " > '".CMbDT::dateTime()."' OR date_max IS NULL";
  $where["date_min"] = " < '".CMbDT::dateTime()."' OR date_min IS NULL";
}

$order = "praticien_id, function_id";
$regles = $regleSector->loadList($where, $order);

//mass load
CStoredObject::massLoadFwdRef($regles, "praticien_id");
CStoredObject::massLoadFwdRef($regles, "service_id");
CStoredObject::massLoadFwdRef($regles, "function_id");

/**
 * @var CRegleSectorisation $_regle
 */
foreach ($regles as $_regle) {
  $_regle->loadRefPraticien()->loadRefFunction();
  $_regle->loadRefService();
  $_regle->loadRefFunction();
  $_regle->checkOlder();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("regles", $regles);
$smarty->assign("show_inactive", $showinactive);
$smarty->display("vw_sectorisations.tpl");