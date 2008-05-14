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
$tab_protocoles_function = array();
$protocoles_function = array();

$protocole = new CPrescription();

if(!$function_id && !$praticien_id && $protocoleSel_id){
  $protocole->load($protocoleSel_id);
  $praticien_id = $protocole->praticien_id;	
  $function_id = $protocole->function_id;
}

if($praticien_id){
	$where = array();
  $where["praticien_id"] = " = '$praticien_id'";
  $where["object_id"] = "IS NULL";
  $tabProtocoles = $protocole->loadList($where);
  foreach($tabProtocoles as $_protocole){
  	$protocoles[$_protocole->object_class][$_protocole->_id] = $_protocole;
  }

  // Chargement des protocoles du cabinet du praticien
  $praticien = new CMediusers();
  $praticien->load($praticien_id);
  $prescription = new CPrescription();
  $where = array();
  $where["function_id"] = " = '$praticien->function_id'";
  $where["object_id"] = "IS NULL";
  $tab_protocoles_function = $prescription->loadList($where);
  
  foreach($tab_protocoles_function as $_protocole){
  	$protocoles_function[$_protocole->object_class][$_protocole->_id] = $_protocole;
  }
}


if($function_id){
	$where = array();
  $where["function_id"] = " = '$function_id'";
  $where["object_id"] = "IS NULL";
  $tab_protocoles_function = $protocole->loadList($where);
  foreach($tab_protocoles_function as $_protocole){
  	$protocoles_function[$_protocole->object_class][$_protocole->_id] = $_protocole;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("protocoles_function", $protocoles_function);
$smarty->assign("protocoles", $protocoles);
$smarty->assign("protocoleSel_id", $protocoleSel_id);

$smarty->display("inc_vw_list_protocoles.tpl");

?>