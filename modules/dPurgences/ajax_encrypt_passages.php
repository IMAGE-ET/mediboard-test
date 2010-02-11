<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage oscour
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license OXPL
 */

global $can;

$can->needsAdmin();

$extract_passages_id = CValue::get("extract_passages_id");

if ($extract_passages_id) {
  $extractPassages = new CExtractPassages();
  $extractPassages->load($extract_passages_id);
}

if (!$extractPassages->_id) {
  CAppUI::stepAjax("Impossible de charger le document XML.", UI_MSG_ERROR);
}

if (!$extractPassages->message_valide) {
  CAppUI::stepAjax("Impossible d'encrypter le message XML car le message n'est pas valide.", UI_MSG_ERROR);
}

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
$extractPassages = $rpuSender->encrypt($extractPassages);

echo "<script type='text/javascript'>extract_passages_id = $extractPassages->_id;</script>"

?>