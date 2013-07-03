<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$operation_id = CValue::getOrSession("operation_id");
$date  = CValue::getOrSession("date", CMbDT::date());
$modif_operation = CCanDo::edit() || $date >= CMbDT::date();

$operation = new COperation();
if ($operation_id) {
  $operation->load($operation_id);
  $operation->loadRefs();
  $operation->loadBrancardage();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp",           $operation);
$smarty->assign("date",            $date);
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("inc_vw_timing.tpl");
