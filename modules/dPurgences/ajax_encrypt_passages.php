<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$extract_passages_id = CValue::get("extract_passages_id");
$view                = CValue::get("view", 0);

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

if ($view) {
  $extractPassages->loadRefsFiles();

  // Création du template
  $smarty = new CSmartyDP("modules/dPurgences");
  $smarty->assign("_passage", $extractPassages);

  $smarty->display("inc_extract_file.tpl");
}
else {
  echo "<script>extract_passages_id = $extractPassages->_id;</script>";
}

