<?php /* $Id: ajax_pack_autocomplete.php $ */

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
$keywords = CValue::post("keywords_pack");

$pack = new CPack();
$where = array();
$where["object_class"] = "= '$object_class'";
$where[] = "(pack.chir_id = $user_id
  OR pack.function_id = $function_id
  OR pack.group_id = $group_id)";

$order = "nom";

$packs = $pack->seek($keywords, $where, null, null, null, $order);

$smarty = new CSmartyDP();

$smarty->assign("packs", $packs);
$smarty->assign("nodebug", true);
$smarty->assign("keywords", $keywords);
$smarty->display("inc_pack_autocomplete.tpl");