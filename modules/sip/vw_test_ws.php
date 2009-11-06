<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7248 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$operation = CValue::post("operation");
$entier1   = CValue::post("entier1");
$entier2   = CValue::post("entier2");
$file      = CValue::read($_FILES, "file");

$result = $acquittement = $errors = null;

// Si Client
if (!CAppUI::conf('sip server')) {
  $dest_hprim = new CDestinataireHprim();
  $dest_hprim->type = "sip";
  $dest_hprim->loadMatchingObject();
  
  if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml")) {
    trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
  }  
  
  if ($operation && $entier1 && $entier2) {
    // Récupère le message après l'execution la methode calculatorAuth
    if (null == $result = $client->calculatorAuth($operation, $entier1, $entier2)) {
      trigger_error("Evénement patient impossible sur le SIP : ".$dest_hprim->url);
    }
  }
  
  if (is_array($file)) {
    $document = new CMbXMLDocument();
    $document->load($file["tmp_name"]);
    $msgEvt = utf8_encode($document->saveXML());

    // Récupère le message d'acquittement après l'execution la methode evenementPatient
    if (null == $acquittement = $client->evenementPatient($msgEvt)) {
      trigger_error("Evénement patient impossible sur le SIP : ".$dest_hprim->url);
    }
    
    $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML(utf8_decode($acquittement));  
    $errors = $domGetAcquittement->schemaValidate(null, true);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation", $operation);
$smarty->assign("entier1"  , $entier1);
$smarty->assign("entier2"  , $entier2);
$smarty->assign("result"   , $result);

$smarty->assign("errors"      , $errors);
$smarty->assign("acquittement", $acquittement);

 
$smarty->display("vw_test_ws.tpl");
?>
