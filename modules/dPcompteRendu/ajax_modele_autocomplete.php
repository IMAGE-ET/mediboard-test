<?php /* $Id: ajax_modele_autocomplete.php $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Thomas Despoix
*/

$user_id = CValue::get("user_id");
$function_id = CValue::get("function_id");
$etab = CGroups::loadCurrent();
$group_id = $etab->_id;
$object_class = CValue::get("object_class");
$keywords = CValue::post("keywords_modele");

$compte_rendu = new CCompteRendu;
$where = array();
$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";
$where[] = "(compte_rendu.chir_id = $user_id
  OR compte_rendu.function_id = $function_id
  OR compte_rendu.group_id = $group_id)";

$order = "nom";

$modeles = $compte_rendu->seek($keywords, $where, null, null, null, $order);
mbTrace($modeles, "modeles", true);
$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("nodebug", true);
$smarty->assign("keywords", $keywords);
$smarty->display("inc_modele_autocomplete.tpl");