<?php 

/**
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$tasking_ticket_id = CValue::post("tasking_ticket_id");
$tags_to_add       = CValue::post("tags_to_add");
$tags_to_remove    = CValue::post("tags_to_remove");
$del               = CValue::post("del");

if (!$del) {
  if ($tags_to_add) {
    $tag_ids = explode("|", $tags_to_add);

    foreach ($tag_ids as $_tag_id) {
      $tag_item               = new CTagItem();
      $tag_item->object_id    = $tasking_ticket_id;
      $tag_item->object_class = "CTaskingTicket";
      $tag_item->tag_id       = $_tag_id;

      $tag_item->loadMatchingObject();

      // We add tag only if not already linked
      if ($tag_item->_id) {
        continue;
      }

      // Create the link between task and tag
      if ($msg = $tag_item->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }
  }

  if ($tags_to_remove) {
    $tag_ids = explode("|", $tags_to_remove);

    foreach ($tag_ids as $_tag_id) {
      $tag_item               = new CTagItem();
      $tag_item->object_id    = $tasking_ticket_id;
      $tag_item->object_class = "CTaskingTicket";
      $tag_item->tag_id       = $_tag_id;

      $tag_item->loadMatchingObject();

      if ($tag_item->_id) {
        // Remove the link between task and tag
        if ($msg = $tag_item->delete()) {
          CAppUI::setMsg($msg, UI_MSG_WARNING);
        }
      }
    }
  }
}

$do = new CDoObjectAddEdit("CTaskingTicket", "tasking_ticket_id");
$do->doIt();
