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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("today", mbDate());
$smarty->assign("line" , $line);
$smarty->assign("object_class", $object_class);
$smarty->display("../../dPprescription/templates/line/inc_vw_stop_line.tpl");

?>