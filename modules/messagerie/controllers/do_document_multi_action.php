<?php 

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

// array
$type = CValue::post("type");

$document_string = CValue::post("document");
$documents = explode(",", $document_string);
CMbArray::removeValue("", $documents);

if (!count($documents)) {
  CAppUI::stepAjax("CBioserveurDocument-msg-pls_select_at_least_one_doc", UI_MSG_WARNING);
  return;
}

$archived = 0;
$delete = 0;

foreach ($documents as $_doc) {
  /** @var CDocumentExterne $object */
  $object = CMbObject::loadFromGuid($_doc);

  switch ($type) {

    case 'star':
      $object->starred = 1;
      if ($msg = $object->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
      else {
        $archived ++;
      }
      break;

    case 'archive':
      $object->archived = 1;
      if ($msg = $object->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
      else {
        $archived ++;
      }
      break;

    case 'delete':
      if ($msg = $object->purge()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
      else {
        $delete++;
      }
      break;

    default:
      CAppUI::setMsg("nothing_to_do");
      return;
  }
}

echo CAppUI::getMsg();