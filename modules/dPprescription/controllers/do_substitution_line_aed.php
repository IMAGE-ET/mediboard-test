<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_guid = CValue::post("object_guid");

// Chargement de la ligne à rendre active
$line = CMbObject::loadFromGuid($object_guid);
$line->variante_active = 1;

$msg = $line->store();
CAppUI::displayMsg($msg, "$line->_class-msg-modify");
// Desactivation des autres lignes

// Si la ligne est deja une ligne de substitution
if($line->variante_for_id){
  // On desactive la ligne originale
  $_line = new $line->variante_for_class;
  $_line->load($line->variante_for_id);
  if($_line->variante_active == 1){
    $_line->variante_active = 0;  
    $msg = $_line->store();
    CAppUI::displayMsg($msg, "$line->_class-msg-modify");
  }

  $_line->loadRefsVariantes();
  // On desactive les autres lignes de substitution
  foreach($_line->_ref_variantes as $_lines_sub_by_chap){
    foreach($_lines_sub_by_chap as $_line_sub){
	    if($_line_sub->variante_active && $_line_sub->_id != $line->_id && $_line_sub->variante_active == 1){
	      $_line_sub->variante_active = 0;
	      $msg = $_line_sub->store();
	      CAppUI::displayMsg($msg, "$line->_class-msg-modify");
	    }
    }
  }
}

// Si la ligne est l'originale, on desactive les lignes de substitution
if(!$line->variante_for_id){
  $line->loadRefsVariantes();
  foreach($line->_ref_variantes as $_lines_sub_by_chap){
    foreach($_lines_sub_by_chap as $_line_sub){
	    if($_line_sub->variante_active){
	      $_line_sub->variante_active = 0;
	      $msg = $_line_sub->store();
	      CAppUI::displayMsg($msg, "$line->_class-msg-modify");
	    }
	 }
  }
}

echo CAppUI::getMsg();
CApp::rip();
?>