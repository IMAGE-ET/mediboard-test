<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Board
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$chir = null;
$mediuser = CMediusers::get();

if ($mediuser->isPraticien()) {
  $chir = $mediuser;
}

$chirSel              = CValue::getOrSession("praticien_id", $chir ? $chir->user_id : null);
$all_prats            = CValue::get("all_prats", 0);
$fin                  = CValue::getOrSession("fin"  , CMbDT::date());
$debut                = CValue::getOrSession("debut", CMbDT::date("-1 week", $fin));
$display_not_exported = Cvalue::getOrSession('display_not_exported', 0);

if (!$all_prats) {
  CAppUI::requireModuleFile("dPboard", "inc_board");
}

$smarty = new CSmartyDP();

$smarty->assign("chirSel"             , $chirSel);
$smarty->assign("debut"               , $debut);
$smarty->assign("fin"                 , $fin);
$smarty->assign("all_prats"           , $all_prats);
$smarty->assign('display_not_exported', $display_not_exported);
$smarty->display("../../dPboard/templates/inc_vw_interv_non_cotees.tpl");
