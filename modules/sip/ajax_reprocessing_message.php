<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7453 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI;

$echange_hprim_id         = CValue::get("echange_hprim_id");
$echange_hprim_classname  = CValue::get("echange_hprim_classname");

// Chargement de l'objet
$echange_hprim = new $echange_hprim_classname;
$echange_hprim->load($echange_hprim_id);

$acquittement = CHprimSoapHandler::evenementPatient($echange_hprim->message);

$domGetAcquittement = new CHPrimXMLAcquittementsPatients();
$domGetAcquittement->loadXML(utf8_decode($acquittement));
$doc_valid = $domGetAcquittement->schemaValidate();
if ($doc_valid) {
  $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
}
$echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
  
$echange_hprim->acquittement = $acquittement;

$echange_hprim->store();

CAppUI::setMsg("Message HPRIM retraité", UI_MSG_OK);

echo CAppUI::getMsg();

?>