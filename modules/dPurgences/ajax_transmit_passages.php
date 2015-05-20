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

if (isset($extractPassages) && $extractPassages->_id) {
  $extract_passages_id = $extractPassages->_id;
}

$attente   = 1;
$extractPassages = new CExtractPassages();

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();

if ($extract_passages_id) {
  $extractPassages->load($extract_passages_id);
  if (!$extractPassages->_id) {
    CAppUI::stepAjax("Impossible de charger le document XML.", UI_MSG_ERROR);
  }

  $file = new CFile();
  $file->setObject($extractPassages);
  $file->loadMatchingObject();

  if (!$file->_id) {
    CAppUI::stepAjax("Impossible de récupérer le document.", UI_MSG_ERROR);
  }
  $tentative = 5;
  if ($extractPassages->type == "activite") {
    while ($tentative-- && $rpuSender->transmitActivite($extractPassages)) {
      sleep($attente);
    }
  }
  else {
    while ($tentative-- && $rpuSender->transmit($extractPassages)) {
      sleep($attente);
    }
  }
}
else {
  $leftjoin["files_mediboard"] = "files_mediboard.object_id = extract_passages.extract_passages_id
    AND files_mediboard.object_class = 'CExtractPassages'";

  $where["files_mediboard.file_id"]         = "IS NOT NULL";
  $where["extract_passages.date_echange"]   = "IS NULL";
  $where['extract_passages.message_valide'] = " = '1'";
  $where['extract_passages.group_id']       = " = '".CGroups::loadCurrent()->_id."'";

  $order = "extract_passages.date_extract DESC";

  /** @var CExtractPassages[] $passages */
  $passages = $extractPassages->loadList($where, $order, null, null, $leftjoin);
  foreach ($passages as $_passage) {
    $tentative = 5;
    if ($_passage->type == "activite") {
      while ($tentative-- && $rpuSender->transmitActivite($_passage)) {
        sleep($attente);
      }
    }
    else {
      while ($tentative-- && $rpuSender->transmit($_passage)) {
        sleep($attente);
      }
    }
  }
}
