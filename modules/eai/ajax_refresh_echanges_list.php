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
$exchange_class      = CValue::getOrSession("exchange_class");
$object_id           = CValue::getOrSession("object_id");
$t                   = CValue::getOrSession('types', array());
$statut_acquittement = CValue::getOrSession("statut_acquittement");
$type                = CValue::getOrSession("type");
$evenement           = CValue::getOrSession("evenement");
$group_id            = CValue::getOrSession("group_id");
$page                = CValue::get('page', 0);
$_date_min           = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max           = CValue::getOrSession('_date_max', mbDateTime("+1 day"));
$keywords_msg        = CValue::getOrSession("keywords_msg");
$keywords_ack        = CValue::getOrSession("keywords_ack");

$exchange = new $exchange_class;

// Récupération de la liste des echanges
$itemExchange = new $exchange_class;

$where = array();
if (isset($t["emetteur"])) {
  $where["sender_id"] = " IS NULL";
}
if (isset($t["destinataire"])) {
  $where["receiver_id"] = " IS NULL";
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
if ($evenement && $exchange instanceof CEchangeXML) {
  $where["sous_type"] = " = '".$evenement."'";
}
if ($evenement && $exchange instanceof CExchangeTabular) {
  $where["code"] = " = '".$evenement."'";
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
$ljoin = null;
if ($keywords_msg) {
  $content_exchange = $exchange->loadFwdRef("message_content_id");
  $table            = $content_exchange->_spec->table;
  $ljoin[$table]    = $exchange->_spec->table.".message_content_id = $table.content_id";
  
  $where["$table.content"] = " LIKE '%$keywords_msg%'";
}

if ($keywords_ack) {
  $content_exchange = $exchange->loadFwdRef("acquittement_content_id");
  $table            = $content_exchange->_spec->table;
  $ljoin[$table]    = $exchange->_spec->table.".acquittement_content_id = $table.content_id";
  
  $where["$table.content"] = " LIKE '%$keywords_ack%'";
}  

$group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;
$where["group_id"] = " = '$group_id'";
$exchange->group_id = $group_id;
$exchange->loadRefGroups();

$forceindex[] = "date_production";
$total_exchanges = $itemExchange->countList($where, null, $ljoin, $forceindex);
$order = "date_production DESC";

$exchanges = $itemExchange->loadList($where, $order, "$page, 25", null, $ljoin, $forceindex);
foreach($exchanges as $_exchange) {
  $_exchange->loadRefsBack();
  $_exchange->getObservations();
  $_exchange->loadRefsInteropActor();
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
$smarty->assign("keywords_msg"       , $keywords_msg);
$smarty->assign("keywords_ack"       , $keywords_ack);

$smarty->display("inc_exchanges.tpl");

?>