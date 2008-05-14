<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$praticien_id = mbGetValueFromGet("praticien_id");
$function_id = mbGetValueFromGet("function_id");

$protocoleSel_id = mbGetValueFromGet("protocoleSel_id");
$protocoles = array();

$protocole = new CPrescription();

if(!$praticien_id && !$praticien_id && $protocoleSel_id){
  $protocole->load($protocoleSel_id);
  $praticien_id = $protocole->praticien_id;	
  $function_id = $protocole->function_id;
}

if($praticien_id){
  $where["praticien_id"] = " = '$praticien_id'";
}
if($function_id){
  $where["function_id"] = " = '$function_id'";
}

$where["object_id"] = "IS NULL";
$tabProtocoles = $protocole->loadList($where);
foreach($tabProtocoles as $_protocole){
	$protocoles[$_protocole->object_class][$_protocole->_id] = $_protocole;
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("protocoles", $protocoles);
$smarty->assign("protocoleSel_id", $protocoleSel_id);

$smarty->display("inc_vw_list_protocoles.tpl");

?>