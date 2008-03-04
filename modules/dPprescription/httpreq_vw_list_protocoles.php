<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$praticien_id = mbGetValueFromGet("praticien_id");
$protocoles = array();

$protocole = new CPrescription();
$where["praticien_id"] = " = '$praticien_id'";
$where["object_id"] = "IS NULL";
$tabProtocoles = $protocole->loadList($where);
foreach($tabProtocoles as $_protocole){
	$protocoles[$_protocole->object_class][$_protocole->_id] = $_protocole;
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("protocoles", $protocoles);

$smarty->display("inc_vw_list_protocoles.tpl");

?>