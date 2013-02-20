<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$date    = CValue::getOrSession("date", mbDate());
$bloc_id = CValue::getOrSession("bloc_id");

$modif_operation = CCanDo::edit() || $date >= mbDate();
$hour = mbTime();
$blocs_list = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id) && count($blocs_list)) {
  $bloc = reset($blocs_list);
}

// Chargement de la liste du personnel pour le reveil
$personnels = array();
if (CModule::getActive("dPpersonnel")) {
  $personnel  = new CPersonnel();
  $personnels = $personnel->loadListPers("reveil");
}

// Vérification de la check list journalière
$check_list = CDailyCheckList::getList($bloc, $date);
$check_list->loadItemTypes();
$check_list->loadBackRefs('items');

$where = array('target_class' => "= 'CBlocOperatoire'");
$check_item_category = new CDailyCheckItemCategory();
$check_item_categories = $check_item_category->loadList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("personnels"           , $personnels);
$smarty->assign("check_list"           , $check_list);
$smarty->assign("check_item_categories", $check_item_categories);
$smarty->assign("date"                 , $date);
$smarty->assign("hour"                 , $hour);
$smarty->assign("modif_operation"      , $modif_operation);
$smarty->assign("blocs_list"           , $blocs_list);
$smarty->assign("bloc"                 , $bloc);
$smarty->assign("isImedsInstalled"     , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_reveil.tpl");
