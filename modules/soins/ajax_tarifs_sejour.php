<?php
/**
 * $Id:$
 *
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision:$
 */

CCanDo::checkEdit();
$sejour_id = CValue::get("sejour_id", 0);
$chir_id   = CValue::get("chir_id"  , 0);

$sejour = new CSejour();
$sejour->load($sejour_id);

if ($sejour->_id) {
  $chir = new CMediusers();
  if ($chir_id) {
    $chir->load($chir_id);
  }
  else {
    $chir->load($sejour->praticien_id);
  }

  $sejour->loadRefPraticien();
  $sejour->loadRefsActes();
  $sejour->updateFormFields();
  $sejour->_datetime = CMbDT::dateTime();

  // Récupération des tarifs
  /** @var CTarif $tarif */
  $tarif = new CTarif();
  $tarifs = array();
  $order = "description";
  $where = array();
  $where["chir_id"] = "= '$chir->_id'";
  $tarifs["user"] = $tarif->loadList($where, $order);
  foreach ($tarifs["user"] as $_tarif) {
    /**@var CTarif $_tarif */
    $_tarif->getPrecodeReady();
  }

  $where = array();
  $where["function_id"] = "= '$chir->function_id'";
  $tarifs["func"] = $tarif->loadList($where, $order);
  foreach ($tarifs["func"] as $_tarif) {
    $_tarif->getPrecodeReady();
  }

  if (CAppui::conf("dPcabinet Tarifs show_tarifs_etab")) {
    $where = array();
    $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
    $tarifs["group"] = $tarif->loadList($where, $order);
    foreach ($tarifs["group"] as $_tarif) {
      $_tarif->getPrecodeReady();
    }
  }

  $smarty = new CSmartyDP();
  $smarty->assign("sejour", $sejour);
  $smarty->assign("tarifs", $tarifs);
  $smarty->display("inc_tarifs_sejour.tpl");
}