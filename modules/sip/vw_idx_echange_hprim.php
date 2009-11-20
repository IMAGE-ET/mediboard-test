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
$echange_hprim->_date_min = $_date_min;
$echange_hprim->_date_max = $_date_max;

$echange_hprim->load($echange_hprim_id);
if($echange_hprim->_id) {
	$echange_hprim->loadRefs();	
	
	$domGetEvenement = new CHPrimXMLEvenementsPatients();
  $domGetEvenement->loadXML(utf8_decode($echange_hprim->message));
  $doc_errors_msg = @$domGetEvenement->schemaValidate(null, true);

  if ($echange_hprim->acquittement) {
		$domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML(utf8_decode($echange_hprim->acquittement));
  
    $observations = $domGetAcquittement->getAcquittementObservation();
    $doc_errors_ack[] = @$domGetEvenement->schemaValidate(null, true);
	}
}

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
  $where['date_echange'] = " = ' BETWEEN ".$_date_min." AND ".$_date_max."' "; 
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
  $where["date_production"] = "IS NULL";
}

$total_echange_hprim = $itemEchangeHprim->countList($where);

$order = "date_production DESC";
$listEchangeHprim    = $itemEchangeHprim->loadList($where, $order, "$page, 20");
  
foreach($listEchangeHprim as $_echange_hprim) {
	$_echange_hprim->loadRefNotifications();
	if (!empty($_echange_hprim->_ref_notifications)) {
		foreach ($_echange_hprim->_ref_notifications as $_ref_notification) {
	    $domGetIdSourceObject = new CHPrimXMLEvenementsPatients();
	    $domGetIdSourceObject->loadXML(utf8_decode($_ref_notification->message));
	     
      $id400 = new CIdSante400();
      if ($_echange_hprim->sous_type == "enregistrementPatient" ) {
        $id400->object_class = "CPatient";
        try {
          $_ref_notification->_object_id_permanent = $domGetIdSourceObject->getIdSourceObject("hprim:enregistrementPatient", "hprim:patient");
        } catch(Exception $e) {}
      }
      if ($_echange_hprim->sous_type == "venuePatient" ) {
        $id400->object_class = "CSejour";
        try {
          $_ref_notification->_object_id_permanent = $domGetIdSourceObject->getIdSourceObject("hprim:venuePatient", "hprim:venue");
        } catch(Exception $e) {}
      }
	   
	    $id400->tag = $_ref_notification->emetteur;
	  
	    $id400->id400 = $_ref_notification->_object_id_permanent;
	    $id400->loadMatchingObject();
	    
      $_ref_notification->_object_class = $id400->object_class;
	    $_ref_notification->_object_id = ($id400->object_id) ? $id400->object_id : $_ref_notification->_object_id_permanent;
		}
	}
  
  $domGetIdSourceObject = new CHPrimXMLEvenementsPatients();
  $domGetIdSourceObject->loadXML(utf8_decode($_echange_hprim->message));
  
  $id400 = new CIdSante400();
  $_echange_hprim->_object_id_permanent = null;
  
  switch($_echange_hprim->sous_type) {
    case "enregistrementPatient" :
    	defineObjectIdClass($domGetIdSourceObject, $id400, $_echange_hprim, "CPatient", "hprim:enregistrementPatient", "hprim:patient");
    	break;
    case "venuePatient" :
      defineObjectIdClass($domGetIdSourceObject, $id400, $_echange_hprim, "CSejour", "hprim:venuePatient", "hprim:venue");
      break;
    case "mouvementPatient" :
      defineObjectIdClass($domGetIdSourceObject, $id400, $_echange_hprim, "CSejour", "hprim:mouvementPatient", "hprim:venue");
      break;
    case "fusionVenue" :
      defineObjectIdClass($domGetIdSourceObject, $id400, $_echange_hprim, "CSejour", "hprim:fusionVenue", "hprim:venue");
      break;
    default :
      defineObjectIdClass($domGetIdSourceObject, $id400 ,$_echange_hprim);
  }
  
	$id400->tag = $_echange_hprim->emetteur;
	$id400->id400 = $_echange_hprim->_object_id_permanent;
	$id400->loadMatchingObject();
  
	if (CAppUI::conf('sip server')) {
		$_echange_hprim->_object_id_permanent = $id400->object_id;
	}
  $_echange_hprim->_object_class = $id400->object_class;
	$_echange_hprim->_object_id = ($id400->object_id) ? $id400->object_id : $_echange_hprim->_object_id_permanent;	
}

function defineObjectIdClass($domGetIdSourceObject, &$id400, &$_echange_hprim, $class = null, $node = null, $type = null) {
  $id400->object_class = $class;
  if ($node && ($_echange_hprim->statut_acquittement == "OK")) {
    try {
      $_echange_hprim->_object_id_permanent = $domGetIdSourceObject->getIdSourceObject($node, $type);
    } catch(Exception $e) {}
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("echange_hprim"       , $echange_hprim);
$smarty->assign("observations"        , $observations);
$smarty->assign("listEchangeHprim"    , $listEchangeHprim);
$smarty->assign("total_echange_hprim" , $total_echange_hprim);
$smarty->assign("page"                , $page);
$smarty->assign("selected_types"      , $t);
$smarty->assign("types"               , $types);
$smarty->assign("statut_acquittement" , $statut_acquittement);
$smarty->assign("msg_evenement"       , $msg_evenement);
$smarty->assign("type_evenement"      , $type_evenement);
$smarty->assign("doc_errors_msg"      , $doc_errors_msg);
$smarty->assign("doc_errors_ack"      , $doc_errors_ack);
$smarty->display("vw_idx_echange_hprim.tpl");
?>
