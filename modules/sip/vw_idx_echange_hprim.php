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
$type_evenement      = mbGetValueFromGetOrSession("type_evenement");
$page                = mbGetValueFromGet('page', 1);
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
$echange_hprim->_date_min = $_date_min ? $_date_min : $now;
$echange_hprim->_date_max = $_date_max ? $_date_max : $now;

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

$where['date_echange'] = (($_date_min) && ($_date_max)) ? "BETWEEN '".$_date_min."' AND '".$_date_max."'" : "IS NULL";
$where["statut_acquittement"] = $statut_acquittement ? " = '".$statut_acquittement."'" : "IS NULL";
$where["sous_type"] = $type_evenement ? " = '".$type_evenement."'" : "IS NULL";
$where["message_valide"] = isset($t["message_valide"]) ? " = '1'" : " = '0' OR message_valide IS NULL";
$where["acquittement_valide"] = isset($t["acquittement_valide"]) ? " = '1'" : " = '0' OR acquittement_valide IS NULL";

$total_echange_hprim = $itemEchangeHprim->countList($where);

//Pagination
$total_pages = ceil($total_echange_hprim / 20);

$limit = ($page == 1) ? 0 : $page * 10;
$order = "date_echange DESC";
$listEchangeHprim    = $itemEchangeHprim->loadList($where, $order, intval($limit).',20');
  
foreach($listEchangeHprim as &$curr_echange_hprim) {
	$curr_echange_hprim->loadRefNotifications();
	if (!empty($curr_echange_hprim->_ref_notifications)) {
		foreach ($curr_echange_hprim->_ref_notifications as $_curr_ref_notification) {
	    $domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	    $domGetIPPPatient->loadXML(utf8_decode($_curr_ref_notification->message));
	
	    $_curr_ref_notification->_patient_ipp = $domGetIPPPatient->getIPPPatient("hprim:enregistrementPatient");
	   
	    $id400 = new CIdSante400();
	    //Paramétrage de l'id 400
	    $id400->object_class = "CPatient";
	    $id400->tag = $_curr_ref_notification->emetteur;
	  
	    $id400->id400 = $_curr_ref_notification->_patient_ipp;
	    $id400->loadMatchingObject();
	    
	    $_curr_ref_notification->_patient_id = ($id400->object_id) ? $id400->object_id : $_curr_ref_notification->_patient_ipp;
		}
	} 
	$domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	$domGetIPPPatient->loadXML(utf8_decode($curr_echange_hprim->message));

	$curr_echange_hprim->_patient_ipp = $domGetIPPPatient->getIPPPatient("hprim:enregistrementPatient");
	
	$id400 = new CIdSante400();
	//Paramétrage de l'id 400
	$id400->object_class = "CPatient";
	$id400->tag = $curr_echange_hprim->emetteur;

	$id400->id400 = $curr_echange_hprim->_patient_ipp;
	$id400->loadMatchingObject();
  
	if (CAppUI::conf('sip server')) {
		$curr_echange_hprim->_patient_ipp = $id400->object_id;
	}
	$curr_echange_hprim->_patient_id = ($id400->object_id) ? $id400->object_id : $curr_echange_hprim->_patient_ipp;	
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("echange_hprim"       , $echange_hprim);
$smarty->assign("observations"        , $observations);
$smarty->assign("listEchangeHprim"    , $listEchangeHprim);
$smarty->assign("total_echange_hprim" , intval($total_echange_hprim));
$smarty->assign("total_pages"         , $total_pages);
$smarty->assign("page"                , $page);
$smarty->assign("selected_types"      , $t);
$smarty->assign("types"               , $types);
$smarty->assign("statut_acquittement" , $statut_acquittement);
$smarty->assign("type_evenement"      , $type_evenement);
$smarty->assign("doc_errors_msg"      , $doc_errors_msg);
$smarty->assign("doc_errors_ack"      , $doc_errors_ack);
$smarty->display("vw_idx_echange_hprim.tpl");
?>
