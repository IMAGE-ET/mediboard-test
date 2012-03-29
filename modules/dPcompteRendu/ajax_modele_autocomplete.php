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
$fast_edit    = CValue::get("fast_edit", 1);

$user = new CMediusers();
$user->load($user_id);
$user->loadRefFunction();

$compte_rendu = new CCompteRendu;
$modeles      = array();
$order        = "nom";

$where = array();

if (!$fast_edit) {
  $where["fast_edit"] = " = '0'";
  $where["fast_edit_pdf"] = " = '0'";
}

$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";
$where["user_id"] = "IN ('$user->_id', '".CAppUI::$user->_id."')";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

unset($where["user_id"]);

$where["type"] = "= 'body'";

// Inclusion des fonctions secondaires de l'utilisateur connecté
$sec_function = new CSecondaryFunction;
$sec_function->user_id = CAppUI::$user->_id;
$sec_functions = $sec_function->loadMatchingList();

$functions_ids = CMbArray::pluck($sec_functions, "function_id");

array_merge($functions_ids, array($user->function_id, CAppUI::$user->function_id));

$where["function_id"] = CSQLDataSource::prepareIn($functions_ids);
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

unset($where["function_id"]);

$where["type"] = "= 'body'";
$where["group_id"] = " = '$user->_group_id'";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

$modeles = CStoredObject::naturalSort($modeles, array("nom"), true);

$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("nodebug", true);
$smarty->assign("keywords", $keywords);
$smarty->display("inc_modele_autocomplete.tpl");