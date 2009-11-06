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

$result = null;

// Si Client
if (!CAppUI::conf('sip server')) {
  if ($operation && $entier1 && $entier2) {
    $dest_hprim = new CDestinataireHprim();
    $dest_hprim->type = "sip";
    $dest_hprim->loadMatchingObject();
    
    if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml")) {
      trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
    }
  
    // Récupère le message d'acquittement après l'execution la methode evenementPatient
    if (null == $result = $client->calculatorAuth($operation, $entier1, $entier2)) {
      trigger_error("Evénement patient impossible sur le SIP : ".$dest_hprim->url);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation", $operation);
$smarty->assign("entier1"  , $entier1);
$smarty->assign("entier2"  , $entier2);
$smarty->assign("result" , $result);

$smarty->display("vw_test_ws.tpl");
?>
