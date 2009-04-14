<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$note_id      = mbGetValueFromGet("note_id", null);
$object_class = mbGetValueFromGet("object_class", null);
$object_id    = mbGetValueFromGet("object_id", null);

$note = new CNote;
if($note_id) {
  $note->load($note_id);
} else {
  $note->object_class = $object_class;
  $note->object_id = $object_id;
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