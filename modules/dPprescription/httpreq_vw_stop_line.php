<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
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

// Liste d'heures et de minutes
$hours = range(0,23);
foreach($hours as &$hour){
	$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
}
$mins = range(0,59);
foreach($mins as &$min){
	$min = str_pad($min, 2, "0", STR_PAD_LEFT);
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hours", $hours);
$smarty->assign("mins", $mins);
$smarty->assign("today", mbDate());
$smarty->assign("now", mbDateTime());
$smarty->assign("line" , $line);
$smarty->assign("object_class", $object_class);
$smarty->assign("category", $category);
$smarty->display("../../dPprescription/templates/line/inc_vw_stop_line.tpl");

?>