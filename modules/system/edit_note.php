<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $can;

$user = CUser::get();

$note_id     = CValue::get("note_id");
$object_guid = CValue::get("object_guid");

$note = new CNote;
if ($note_id) {
  $note->load($note_id);
}
else {
  $note->setObject(CMbObject::loadFromGuid($object_guid));
  $note->user_id = $user->_id;
  $note->date = CMbDT::dateTime();
}

$note->loadRefsFwd();
$note->_ref_user->loadRefsFwd();

$can->read = $note->_ref_object->canRead();
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("note", $note);
$smarty->display("edit_note.tpl");
