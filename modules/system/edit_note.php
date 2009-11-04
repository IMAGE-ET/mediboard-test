<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$note_id     = CValue::get("note_id");
$object_guid = CValue::get("object_guid");

$note = new CNote;
if($note_id) {
  $note->load($note_id);
} else {
  $note->setObject(CMbObject::loadFromGuid($object_guid));
  $note->user_id = $AppUI->user_id;
  $note->date = mbDateTime();
}

$note->loadRefsFwd();
$note->_ref_user->loadRefsFwd();

$can->read = $note->_ref_object->canRead();
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("note", $note);
$smarty->display("edit_note.tpl");

?>