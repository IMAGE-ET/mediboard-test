<?php

/**
 * Autocomplete des packs de modèles
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$user_id      = CValue::get("user_id");
$function_id  = CValue::get("function_id");
$etab         = CGroups::loadCurrent();
$group_id     = $etab->_id;
$object_class = CValue::get("object_class");
$keywords     = CValue::post("keywords_pack");

$where = array();
$where["object_class"] = "= '$object_class'";
$where[] = "(
  pack.user_id IN ('".$user_id."', '".CAppUI::$user->_id."') OR
  pack.function_id = '$function_id' OR 
  pack.group_id = '$group_id'
)";
$where[] = "pack.pack_id IN ( SELECT pack_id FROM modele_to_pack)";

$order = "nom";

$pack = new CPack();
$packs = $pack->seek($keywords, $where, null, null, null, $order);

/** @var $_pack CPack */
foreach ($packs as $_pack) {
  $_pack->getModelesIds();
}

$smarty = new CSmartyDP();

$smarty->assign("packs", $packs);
$smarty->assign("nodebug", true);
$smarty->assign("keywords", $keywords);

$smarty->display("inc_pack_autocomplete.tpl");