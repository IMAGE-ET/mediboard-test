<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}

$object = new $object_class;
$object->load($object_id);
$object->loadRefsNotes(PERM_READ);

$high = false;

foreach($object->_ref_notes as $key => $note) {
  if($note->degre == "high"){
    $high = true;
    break;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object" , $object);
$smarty->assign("notes"  , $object->_ref_notes);
$smarty->assign("high"   , $high);
$smarty->display("inc_get_notes_image.tpl");
?>