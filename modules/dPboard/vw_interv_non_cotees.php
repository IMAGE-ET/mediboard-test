<?php

/**
 * dPboard
 *  
 * @category dPboard
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$chir = null;
$mediuser = CMediusers::get();

if ($mediuser->isPraticien()) {
  $chir = $mediuser;
}

$chirSel      = CValue::getOrSession("praticien_id", $chir ? $chir->user_id : null);
$all_prats    = CValue::get("all_prats", 0);

if (!$all_prats) {
  CAppUI::requireModuleFile("dPboard", "inc_board");
}

$fin   = CValue::getOrSession("fin"  , mbDate());
$debut = CValue::getOrSession("debut", mbDate("-1 week", $fin));

$smarty = new CSmartyDP;

$smarty->assign("chirSel", $chirSel);
$smarty->assign("debut", $debut);
$smarty->assign("fin", $fin);
$smarty->assign("all_prats", $all_prats);
$smarty->display("../../dPboard/templates/inc_vw_interv_non_cotees.tpl");

?>