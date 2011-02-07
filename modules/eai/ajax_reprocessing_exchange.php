<?php 
/**
 * Reprocessing exchange
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

CAppUI::setMsg("Cette fonctionnalité est désactivée pour le moment...", UI_MSG_ERROR);

/*
$echange_xml_id         = CValue::get("echange_xml_id");
$echange_xml_classname  = CValue::get("echange_xml_classname");

// Chargement de l'objet
$echange_xml = new $echange_xml_classname;
$echange_xml->load($echange_xml_id);

if ($echange_xml instanceof CEchangeHprim) {
  $acquittement = CHprimSoapHandler::evenementPatient($echange_xml->_message);

  $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
  $domGetAcquittement->loadXML($acquittement);
  $doc_valid = $domGetAcquittement->schemaValidate();
  if ($doc_valid) {
    $echange_xml->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
  }
  $echange_xml->acquittement_valide = $doc_valid ? 1 : 0;
  $echange_xml->_acquittement = $acquittement;
  $echange_xml->store();
  
  CAppUI::setMsg("Message '$echange_xml->_class_name' retraité", UI_MSG_OK);
}
else if ($echange_xml instanceof CPhastEchange) {
  
  
  CAppUI::setMsg("Message '$echange_xml->_class_name' retraité", UI_MSG_OK);
}*/

echo CAppUI::getMsg();

?>