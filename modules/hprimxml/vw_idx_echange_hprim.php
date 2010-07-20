<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echange_hprim_id    = CValue::get("echange_hprim_id");
$id_permanent        = CValue::getOrSession("id_permanent");
$t                   = CValue::getOrSession('types', array());
$statut_acquittement = CValue::getOrSession("statut_acquittement");
$msg_evenement       = CValue::getOrSession("msg_evenement", "patients");
$type_evenement      = CValue::getOrSession("type_evenement");
$page                = CValue::get('page', 0);
$_date_min           = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max           = CValue::getOrSession('_date_max', mbDateTime("+1 hour"));

$observations = array();

// Types filtres qu'on peut prendre en compte
$filtre_types = array('no_date_echange', 'emetteur', 'destinataire', 'message_invalide', 'acquittement_invalide');

$types = array();
foreach ($filtre_types as $type) {
  $types[$type] = !isset($t) || in_array($type, $t);
}

$doc_errors_msg = $doc_errors_ack = "";

// Chargement de l'échange HPRIM demandé
$echange_hprim = new CEchangeHprim();
$echange_hprim->id_permanent = $id_permanent;
$echange_hprim->_date_min = $_date_min;
$echange_hprim->_date_max = $_date_max;

$echange_hprim->load($echange_hprim_id);

// Création du template
$smarty = new CSmartyDP();
  
if($echange_hprim->_id) {
  $observations = $doc_errors_msg = $doc_errors_ack = array();
  
  $echange_hprim->loadRefs(); 

  if ($echange_hprim->_message !== null) {
    $domGetEvenement = new CHPrimXMLEvenementsPatients();
    $domGetEvenement->loadXML(utf8_decode($echange_hprim->_message));
    $domGetEvenement->formatOutput = true;
    $doc_errors_msg = @$domGetEvenement->schemaValidate(null, true, false);
   
    $echange_hprim->_message = utf8_encode($domGetEvenement->saveXML());
  }  
  if ($echange_hprim->_acquittement !== null) {
    $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML(utf8_decode($echange_hprim->_acquittement));
    $domGetAcquittement->formatOutput = true;
    $observations = $domGetAcquittement->getAcquittementObservationPatients();
    $doc_errors_ack[] = @$domGetAcquittement->schemaValidate(null, true, false);
    
    $echange_hprim->_acquittement = utf8_decode($domGetAcquittement->saveXML());
  } 
   
  $smarty->assign("observations"  , $observations);
  $smarty->assign("doc_errors_msg", $doc_errors_msg);
  $smarty->assign("doc_errors_ack", $doc_errors_ack);
  $smarty->assign("echange_hprim" , $echange_hprim); 
} else {
  // Récupération de la liste des echanges HPRIM
  $itemEchangeHprim = new CEchangeHprim;
  
  $where = array();
  if (isset($t["emetteur"])) {
    $where["emetteur"] = " = '".CAppUI::conf('mb_id')."'";
  }
  if (isset($t["destinataire"])) {
    $where["destinataire"] = " = '".CAppUI::conf('mb_id')."'";
  }
  if ($_date_min && $_date_max) {
    $where['date_production'] = " BETWEEN '".$_date_min."' AND '".$_date_max."' "; 
  }
  if ($statut_acquittement) {
    $where["statut_acquittement"] = " = '".$statut_acquittement."'";
  }
  if ($msg_evenement) {
    $where["type"] = " = '".$msg_evenement."'";
  }
  if ($type_evenement) {
    $where["sous_type"] = " = '".$type_evenement."'";
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
  
  $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";

  $total_echange_hprim = $itemEchangeHprim->countList($where);
  $order = "date_production DESC";
  $forceindex[] = "date_production";

  $echangesHprim = $itemEchangeHprim->loadList($where, $order, "$page, 20", null, null, $forceindex);
    
  foreach($echangesHprim as $_echange) {
    $_echange->loadRefNotifications();
    $_echange->getObservations();
  }
  
  $smarty->assign("echange_hprim"       , $echange_hprim);
  $smarty->assign("echangesHprim"       , $echangesHprim);
  $smarty->assign("total_echange_hprim" , $total_echange_hprim);
  $smarty->assign("page"                , $page);
  $smarty->assign("selected_types"      , $t);
  $smarty->assign("types"               , $types);
  $smarty->assign("statut_acquittement" , $statut_acquittement);
  $smarty->assign("msg_evenement"       , $msg_evenement);
  $smarty->assign("type_evenement"      , $type_evenement);
}

$evenements = array();
foreach (CEchangeHprim::$messages as $_message => $_evt_class) {
  $class = new ReflectionClass($_evt_class);
  $statics = $class->getStaticProperties();
  $evenements[$_message] = $statics["evenements"];
}
$smarty->assign("evenements", $evenements);

$smarty->display("vw_idx_echange_hprim.tpl");

?>
