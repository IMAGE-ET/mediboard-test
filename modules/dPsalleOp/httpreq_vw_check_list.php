<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$check_list_id = CValue::get("check_list_id");

$check_list = new CDailyCheckList();
$check_list->load($check_list_id);
$check_list->loadItemTypes();
$check_list->loadRefsFwd();
$check_list->loadBackRefs('items');

$anesth_id = null;

if ($check_list->object_class == "COperation") {
  $check_list->_ref_object->loadRefChir();
  $anesth_id = $check_list->_ref_object->anesth_id;
}
$anesth = new CMediusers();
$anesth->load($anesth_id);

$type_personnel = array("op", "op_panseuse");
if (!$check_list->validator_id && $check_list->_id) {
  $check_list->loadRefListType();
  $validateurs = explode("|", $check_list->_ref_list_type->type_validateur);
  $type_personnel = array();
  foreach ($validateurs as $valid) {
    $type_personnel[] = $valid;
  }
}
$personnel = CPersonnel::loadListPers(array_unique(array_values($type_personnel)), true, true);

// Chargement des praticiens
$listChirs = new CMediusers();
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

// Chargement des anesths
$listAnesths = new CMediusers();
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$check_item_category = new CDailyCheckItemCategory;
$check_item_category->target_class = $check_list->object_class;
$check_item_category->type = $check_list->type;
$check_item_categories = $check_item_category->loadMatchingList("title");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("check_list"           , $check_list);
$smarty->assign("personnel"            , $personnel);
$smarty->assign("list_chirs"           , $listChirs);
$smarty->assign("list_anesths"         , $listAnesths);
$smarty->assign("anesth_id"            , $anesth_id);
$smarty->assign("anesth"               , $anesth);
$smarty->assign("check_item_categories", $check_item_categories);
$smarty->display("inc_edit_check_list.tpl");
