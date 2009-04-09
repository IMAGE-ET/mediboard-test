<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_id = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");

$line = new $object_class;
$line->load($object_id);
$line->loadRefLogDateArret();

$category = new CCategoryPrescription();

if($line->_class_name == "CPrescriptionLineElement"){
	$line->loadRefElement();
	$line->_ref_element_prescription->loadRefCategory();
	$category = $line->_ref_element_prescription->_ref_category_prescription;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("today", mbDate());
$smarty->assign("now_time", mbTime());
$smarty->assign("now", mbDateTime());
$smarty->assign("line" , $line);
$smarty->assign("object_class", $object_class);
$smarty->assign("category", $category);
$smarty->display("../../dPprescription/templates/line/inc_vw_stop_line.tpl");

?>