<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Alexis Granger
* @abstract Permet de choisir des modèles pour constituer des packs
*/

CCanDo::checkRead();

$object_guid  = CValue::get("object_guid");
$pack_id      = CValue::get("pack_id");
$filter_class = CValue::get("filter_class");

$object = CMbObject::loadFromGuid($object_guid, true); 
$type = "";
$name_object_id = "";
$types = array();

switch($object->_class) {
	case "CMediusers" :
    $types["prat"] = array("user_id", $object->_id);
    $types["func"] = array("function_id", $object->loadRefFunction()->_id);
    $types["etab"] = array("group_id", CGroups::loadCurrent()->_id);
		break;
	case "CFunctions" :
	  $types["func"] = array("function_id", $object->_id);
      $types["etab"] = array("group_id", CGroups::loadCurrent()->_id);
		break;
	case "CGroups" :
	  $types["etab"] = array("group_id", $object->_id);
}

// Chargement du pack
$pack = new CPack();
$pack->load($pack_id);
$pack->loadRefsFwd();

// Modèles concernés
$modeles = array("prat"=>array(), "func"=>array(), "etab"=>array());

$compte_rendu = new CCompteRendu;
$order        = "nom";

foreach ($types as $_type => $_content) {
  $where        = array();
  $id_field = $_content[0];
  $id_value = $_content[1];
  
  $where["object_id"]     = " IS NULL";
  $where["object_class"]  = " = '$filter_class'";
  $where[$id_field] = "= '$id_value'";
  
  $modeles[$_type] = $compte_rendu->loadlist($where, $order);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("pack"   , $pack   );
$smarty->assign("filter_class", $filter_class);

$smarty->display("inc_list_modeles.tpl");
