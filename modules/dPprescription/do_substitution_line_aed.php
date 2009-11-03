<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI;

$object_guid = CValue::post("object_guid");

// Chargement de la ligne à rendre active
$line = CMbObject::loadFromGuid($object_guid);
$line->substitution_active = 1;

$msg = $line->store();
$AppUI->displayMsg($msg, "$line->_class_name-msg-modify");
// Desactivation des autres lignes

// Si la ligne est deja une ligne de substitution
if($line->substitute_for_id){
  // On desactive la ligne originale
  $_line = new $line->substitute_for_class;
  $_line->load($line->substitute_for_id);
  if($_line->substitution_active == 1){
    $_line->substitution_active = 0;  
    $msg = $_line->store();
    $AppUI->displayMsg($msg, "$line->_class_name-msg-modify");
  }

  $_line->loadRefsSubstitutionLines();
  // On desactive les autres lignes de substitution
  foreach($_line->_ref_substitution_lines as $_lines_sub_by_chap){
    foreach($_lines_sub_by_chap as $_line_sub){
	    if($_line_sub->substitution_active && $_line_sub->_id != $line->_id && $_line_sub->substitution_active == 1){
	      $_line_sub->substitution_active = 0;
	      $msg = $_line_sub->store();
	      $AppUI->displayMsg($msg, "$line->_class_name-msg-modify");
	    }
    }
  }
}

// Si la ligne est l'originale, on desactive les lignes de substitution
if(!$line->substitute_for_id){
  $line->loadRefsSubstitutionLines();
  foreach($line->_ref_substitution_lines as $_lines_sub_by_chap){
    foreach($_lines_sub_by_chap as $_line_sub){
	    if($_line_sub->substitution_active){
	      $_line_sub->substitution_active = 0;
	      $msg = $_line_sub->store();
	      $AppUI->displayMsg($msg, "$line->_class_name-msg-modify");
	    }
	 }
  }
}

echo $AppUI->getMsg();
CApp::rip();
?>