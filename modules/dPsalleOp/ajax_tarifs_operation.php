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

CCanDo::checkEdit();

$operation_id = CValue::get("operation_id", 0);

$operation = new COperation();
$operation->load($operation_id);

if ($operation->_id) {
  $operation->loadRefPraticien();
  $operation->loadRefsActes();
  $operation->updateFormFields();

  // Récupération des tarifs
  /** @var CTarif $tarif */
  $tarif = new CTarif;
  $tarifs = array();
  $order = "description";
  $where = array();
  $where["chir_id"] = "= '$operation->chir_id'";
  $tarifs["user"] = $tarif->loadList($where, $order);
  foreach ($tarifs["user"] as $_tarif) {
    $_tarif->getPrecodeReady();
  }

  $where = array();
  $where["function_id"] = "= '$operation->chir_id'";
  $tarifs["func"] = $tarif->loadList($where, $order);
  foreach ($tarifs["func"] as $_tarif) {
    $_tarif->getPrecodeReady();
  }

  if (CAppUI::conf("dPcabinet Tarifs show_tarifs_etab")) {
    $where = array();
    $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
    $tarifs["group"] = $tarif->loadList($where, $order);
    foreach ($tarifs["group"] as $_tarif) {
      $_tarif->getPrecodeReady();
    }
  }

  $smarty = new CSmartyDP();
  $smarty->assign("operation", $operation);
  $smarty->assign("tarifs", $tarifs);
  $smarty->display("inc_tarifs_operation.tpl");
}
