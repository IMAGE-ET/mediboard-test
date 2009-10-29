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

$echange_hprim_id    = mbGetValueFromGet("echange_hprim_id");
$t                   = mbGetValueFromGetOrSession('types', array());
$statut_acquittement = mbGetValueFromGetOrSession("statut_acquittement");
$msg_evenement       = mbGetValueFromGetOrSession("msg_evenement");
$type_evenement      = mbGetValueFromGetOrSession("type_evenement");
$page                = mbGetValueFromGet('page', 0);
$now                 = mbDate();
$_date_min           = mbGetValueFromGetOrSession('_date_min');
$_date_max           = mbGetValueFromGetOrSession('_date_max');

$observations = array();

// Types filtres qu'on peut prendre en compte
$filtre_types = array('emetteur', 'destinataire', 'message_valide', 'acquittement_valide');

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
}
if (isset($t["message_valide"])) {
  $where["message_valide"] = " = '1'";
}
if (isset($t["acquittement_valide"])) {
  $where["message_valide"] = " = '1'";
}

$total_echange_hprim = $itemEchangeHprim->countList($where);

$order = "date_echange DESC";
$listEchangeHprim    = $itemEchangeHprim->loadList($where, $order, "$page, 20");
  
foreach($listEchangeHprim as $_echange_hprim) {
	$_echange_hprim->loadRefNotifications();
	if (!empty($_echange_hprim->_ref_notifications)) {
		foreach ($_echange_hprim->_ref_notifications as $_ref_notification) {
	    $domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	    $domGetIPPPatient->loadXML(utf8_decode($_ref_notification->message));
	
	    $_ref_notification->_patient_ipp = $domGetIPPPatient->getIPPPatient("hprim:enregistrementPatient");
	   
	    $id400 = new CIdSante400();
	    //Paramétrage de l'id 400
	    $id400->object_class = "CPatient";
	    $id400->tag = $_ref_notification->emetteur;
	  
	    $id400->id400 = $_ref_notification->_patient_ipp;
	    $id400->loadMatchingObject();
	    
	    $_ref_notification->_patient_id = ($id400->object_id) ? $id400->object_id : $_ref_notification->_patient_ipp;
		}
	} 
	$domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	$domGetIPPPatient->loadXML(utf8_decode($_echange_hprim->message));

	$_echange_hprim->_patient_ipp = $domGetIPPPatient->getIPPPatient("hprim:enregistrementPatient");
	
	$id400 = new CIdSante400();
	//Paramétrage de l'id 400
	$id400->object_class = "CPatient";
	$id400->tag = $_echange_hprim->emetteur;

	$id400->id400 = $_echange_hprim->_patient_ipp;
	$id400->loadMatchingObject();
  
	if (CAppUI::conf('sip server')) {
		$_echange_hprim->_patient_ipp = $id400->object_id;
	}
	$_echange_hprim->_patient_id = ($id400->object_id) ? $id400->object_id : $_echange_hprim->_patient_ipp;	
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
