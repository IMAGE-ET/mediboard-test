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
$_date_max           = CValue::getOrSession('_date_max', mbDateTime("+1 day"));

$observations  = array();
$echangesHprim = null;

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
  
$observations = $doc_errors_msg = $doc_errors_ack = array();

$echange_hprim->loadRefs(); 
$echange_hprim->loadRefsDestinataireHprim();

if ($echange_hprim->_message !== null) {
  $domGetEvenement = null;
  $echange_hprim->type == "patients" ?
    $domGetEvenement = new CHPrimXMLEvenementsPatients() : null;
  $echange_hprim->type == "pmsi" ?
    $domGetEvenement = new CHPrimXMLEvenementsServeurActivitePmsi::$evenements[$echange_hprim->sous_type] : null;
    
  $domGetEvenement->loadXML(utf8_decode($echange_hprim->_message));
  $domGetEvenement->formatOutput = true;
  $doc_errors_msg = $domGetEvenement->schemaValidate(null, true, false);
 
  $echange_hprim->_message = utf8_encode($domGetEvenement->saveXML());
}  
if ($echange_hprim->_acquittement !== null) {
  $echange_hprim->type == "patients" ?
    $domGetAcquittement = new CHPrimXMLAcquittementsPatients() : null;
  $echange_hprim->type == "pmsi" ?
    $domGetAcquittement = new CHPrimXMLAcquittementsServeurActivitePmsi::$evenements[$echange_hprim->sous_type] : null;
  $domGetAcquittement->loadXML(utf8_decode($echange_hprim->_acquittement));
  $domGetAcquittement->formatOutput = true;
  $echange_hprim->type == "patients" ?
    $observations = $domGetAcquittement->getAcquittementObservationPatients() : array();
  $doc_errors_ack[] = @$domGetAcquittement->schemaValidate(null, true, false);
  
  $echange_hprim->_acquittement = utf8_encode($domGetAcquittement->saveXML());
}

$evenements = array();
foreach (CEchangeHprim::$messages as $_message => $_evt_class) {
  $class = new ReflectionClass($_evt_class);
  $statics = $class->getStaticProperties();
  $evenements[$_message] = $statics["evenements"];
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("echange_hprim"       , $echange_hprim);
$smarty->assign("selected_types"      , $t);
$smarty->assign("page"                , $page);
$smarty->assign("types"               , $types);
$smarty->assign("statut_acquittement" , $statut_acquittement);
$smarty->assign("msg_evenement"       , $msg_evenement);
$smarty->assign("type_evenement"      , $type_evenement);
$smarty->assign("evenements"          , $evenements);
$smarty->assign("observations"        , $observations);
$smarty->assign("doc_errors_msg"      , $doc_errors_msg);
$smarty->assign("doc_errors_ack"      , $doc_errors_ack);

$smarty->display("vw_idx_echange_hprim.tpl");

?>
