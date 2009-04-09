<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI;

$category_id = mbGetValueFromPost("category_id");
$category_dest_id = mbGetValueFromPost("category_dest_id");

// Chargement des elements de la categorie selectionnée
$element = new CElementPrescription();
$element->category_prescription_id = $category_id;
$elements = $element->loadMatchingList();

foreach($elements as $element){
  $element->_id = '';
  $element->category_prescription_id = $category_dest_id;
  $msg = $element->store();
  $AppUI->displayMsg($msg, "CElementPrescription-msg-create");
}

// Redirection vers la categorie de destination
$AppUI->redirect("m=dPprescription&tab=vw_edit_element&category_id=$category_dest_id");

?>