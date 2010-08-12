<?php /* $Id: ajax_modele_autocomplete.php $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Thomas Despoix
*/

$user_id      = CValue::get("user_id");
$object_class = CValue::get("object_class");
$keywords     = CValue::post("keywords_modele");

$user = new CMediusers();
$user->load($user_id);
$user->loadRefFunction();

$compte_rendu = new CCompteRendu;
$modeles      = array();
$order        = "nom";

$where = array();
$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";
$where["chir_id"] = " = '$user->_id'";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

$where = array();
$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";
$where["function_id"] = " = '$user->function_id'";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

$where = array();
$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";
$where["group_id"] = " = '$user->_group_id'";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("nodebug", true);
$smarty->assign("keywords", $keywords);
$smarty->display("inc_modele_autocomplete.tpl");