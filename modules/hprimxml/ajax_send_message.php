<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echange_hprim_id         = CValue::get("echange_hprim_id");
$echange_hprim_classname  = CValue::get("echange_hprim_classname");

$where = '';
if (!$echange_hprim_id) {
  if (!($limit = CAppUI::conf('sip batch_count'))) {
    return;
  }
	$echange_hprim = new CEchangeHprim();
  $where['statut_acquittement'] = "IS NULL";
	$where['emetteur'] = " = '".CAppUI::conf("mb_id")."'";
	$where['message_valide'] = " = '1'";
  $where['acquittement_valide'] = "IS NULL"; 
	
  $notifications = $echange_hprim->loadList($where, null, $limit);
  // Effectue le traitement d'enregistrement des notifications sur lequel le cron vient de passer
  // ce qui permet la gestion des doublons
  foreach ($notifications as $notification) {
  	$notification->date_echange = mbDateTime();
    $notification->store();
  }
  
  foreach ($notifications as $notification) {      
    $dest_hprim = new CDestinataireHprim();
	  $dest_hprim->nom = $notification->destinataire;
	  
	  $dest_hprim->loadMatchingObject();
    
    if ($dest_hprim->actif) {
      $source = CExchangeSource::get($dest_hprim->_guid);
      $source->setData($notification->message);
      $source->send("evenementPatient");
      $acquittement = $source->receive();
      
      if ($acquittement) {
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
        $domGetAcquittement->loadXML(utf8_decode($acquittement));
        $doc_valid = $domGetAcquittement->schemaValidate();
        if ($doc_valid) {
          $notification->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
        }
        $notification->acquittement_valide = $doc_valid ? 1 : 0;
        
        $notification->date_echange = mbDateTime();
        $notification->acquittement = $acquittement;
        $notification->store();
      }      
    }
  }
} else {
	// Chargement de l'objet
	$echange_hprim = new $echange_hprim_classname;
	$echange_hprim->load($echange_hprim_id);
  
	$dest_hprim = new CDestinataireHprim();
	$dest_hprim->nom = $echange_hprim->destinataire;
	$dest_hprim->loadMatchingObject();
  
  $source = CExchangeSource::get($dest_hprim->_guid);
  $source->setData($echange_hprim->message);
  $source->send("evenementPatient");
  $acquittement = $source->receive();
  
  if ($acquittement) {
    $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML(utf8_decode($acquittement));
    $doc_valid = $domGetAcquittement->schemaValidate();
    if ($doc_valid) {
      $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
    }
    $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->acquittement = $acquittement;
  
    $echange_hprim->store();
    
    CAppUI::setMsg("Message HPRIM envoyé", UI_MSG_OK);
    
    echo CAppUI::getMsg();
  }
}

?>