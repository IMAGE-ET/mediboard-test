<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$echange_hprim_id    = CValue::get("echange_hprim_id");
$id_permanent        = CValue::getOrSession("id_permanent");
$t                   = CValue::getOrSession('types', array());
$statut_acquittement = CValue::getOrSession("statut_acquittement");
$msg_evenement       = CValue::getOrSession("msg_evenement", "patients");
$type_evenement      = CValue::getOrSession("type_evenement");
$page                = CValue::get('page', 0);
$now                 = mbDate();
$_date_min           = CValue::getOrSession('_date_min');
$_date_max           = CValue::getOrSession('_date_max');

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
	$echange_hprim->loadRefs();	
  
	$domGetEvenement = new CHPrimXMLEvenementsPatients();
  $domGetEvenement->loadXML(utf8_decode($echange_hprim->message));
  $doc_errors_msg = @$domGetEvenement->schemaValidate(null, true, false);

  if ($echange_hprim->acquittement) {
	  $observations = $echange_hprim->getObservations();
    $doc_errors_ack[] = @$domGetEvenement->schemaValidate(null, true, false);
	}  
  
  $smarty->assign("echange_hprim"       , $echange_hprim);
  $smarty->assign("observations"        , $observations);
  $smarty->assign("doc_errors_msg"      , $doc_errors_msg);
  $smarty->assign("doc_errors_ack"      , $doc_errors_ack);
 
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
    $where['date_echange'] = " BETWEEN '".$_date_min."' AND '".$_date_max."' "; 
  }
  if ($statut_acquittement) {
    $where["statut_acquittement"] = " = '".$statut_acquittement."'";
  }
  if ($type_evenement) {
    $where["sous_type"] = " = '".$type_evenement."'";
  } else {
    $where["sous_type"] = "IS NOT NULL";
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
  $listEchangeHprim    = $itemEchangeHprim->loadList($where, $order, "$page, 20");
    
  foreach($listEchangeHprim as $_echange_hprim) {
    $_echange_hprim->loadRefNotifications();
    $_echange_hprim->getObservations();
  }
  
  $smarty->assign("echange_hprim"       , $echange_hprim);
  $smarty->assign("listEchangeHprim"    , $listEchangeHprim);
  $smarty->assign("total_echange_hprim" , $total_echange_hprim);
  $smarty->assign("page"                , $page);
  $smarty->assign("selected_types"      , $t);
  $smarty->assign("types"               , $types);
  $smarty->assign("statut_acquittement" , $statut_acquittement);
  $smarty->assign("msg_evenement"       , $msg_evenement);
  $smarty->assign("type_evenement"      , $type_evenement);
}

$smarty->display("vw_idx_echange_hprim.tpl");

?>
