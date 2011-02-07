<?php 
/**
 * Refresh exchange XML
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$id_permanent        = CValue::getOrSession("id_permanent");
$exchange_class_name = CValue::getOrSession("exchange_class_name");
$object_id           = CValue::getOrSession("object_id");
$t                   = CValue::getOrSession('types', array());
$statut_acquittement = CValue::getOrSession("statut_acquittement");
$type                = CValue::getOrSession("type");
$evenement           = CValue::getOrSession("evenement");
$page                = CValue::get('page', 0);
$_date_min           = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max           = CValue::getOrSession('_date_max', mbDateTime("+1 day"));

if (!$type) {
  CAppUI::stepAjax("exchange-choose-type", UI_MSG_WARNING);
  return;
}

$exchange = new $exchange_class_name;

// Récupération de la liste des echanges
$itemExchange = new $exchange_class_name;

$where = array();
if (isset($t["emetteur"])) {
  $where["emetteur_id"] = " IS NULL";
}
if (isset($t["destinataire"])) {
  $where["destinataire_id"] = " IS NULL";
}
if ($_date_min && $_date_max) {
  $where['date_production'] = " BETWEEN '".$_date_min."' AND '".$_date_max."' "; 
}
if ($statut_acquittement) {
  $where["statut_acquittement"] = " = '".$statut_acquittement."'";
}
if ($type) {
  $where["type"] = " = '".$type."'";
}
if ($evenement) {
  $where["sous_type"] = " = '".$evenement."'";
}
if (isset($t["message_invalide"])) {
  $where["message_valide"] = " = '0'";
}
if (isset($t["acquittement_invalide"])) {
  $where["acquittement_valide"] = " = '0'";
}
if (isset($t["no_date_echange"])) {
  $where["date_echange"] = "IS NULL";
}
if ($id_permanent) {
  $where["id_permanent"] = " = '$id_permanent'";
}
if ($object_id) {
  $where["object_id"] = " = '$object_id'";
}
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$total_exchanges = $itemExchange->countList($where);
$order = "date_production DESC";
$forceindex[] = "date_production";

$exchanges = $itemExchange->loadList($where, $order, "$page, 20", null, null, $forceindex);
  
foreach($exchanges as $_exchange) {
  $_exchange->loadRefsBack();
  $_exchange->getObservations();
  $_exchange->loadRefsDestinataireInterop();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("exchange"           , $exchange);
$smarty->assign("exchanges"          , $exchanges);
$smarty->assign("total_exchanges"    , $total_exchanges);
$smarty->assign("page"               , $page);
$smarty->assign("selected_types"     , $t);
$smarty->assign("statut_acquittement", $statut_acquittement);
$smarty->assign("type"               , $type);
$smarty->assign("evenement"          , $evenement);

$smarty->display("inc_exchanges.tpl");

?>