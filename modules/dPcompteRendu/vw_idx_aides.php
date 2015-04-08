<?php

/**
 * Interface des aides à la saisie
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Utilisateur sélectionné ou utilisateur courant
$filter_user_id = CValue::getOrSession("filter_user_id");
$filter_class   = CValue::getOrSession("filter_class");
$aide_id        = CValue::getOrSession("aide_id", "0");
$keywords       = CValue::getOrSession("keywords");
$start          = CValue::getOrSession("start", array("user" => 0, "func" => 0, "etab" => 0));
$order_col_aide = CValue::getOrSession("order_col_aide", "class");
$order_way      = CValue::getOrSession("order_way", "ASC");

$listOrderCols = array("class", "field", "depend_value_1", "depend_value_2", "name");

if (!in_array($order_col_aide, $listOrderCols)) {
  $order_col_aide = "class";
}

$classes = array_flip(CApp::getInstalledClasses());
$listTraductions = array();

// Chargement des champs d'aides a la saisie
foreach ($classes as $class => &$infos) {
  $listTraductions[$class] = CAppUI::tr($class);
  $object = new $class;
  $infos = array();
  foreach ($object->_specs as $field => $spec) {
    if (!isset($spec->helped)) {
      continue;
    }
    $info =& $infos[$field];
    $helped = $spec->helped;

    if (!is_array($helped)) {
      $info = null;
      continue;
    }

    foreach ($helped as $i => $depend_field) {
      $key = "depend_value_" . ($i+1);
      $info[$key] = array();
      $list = &$info[$key];
      $list = array();
      // Because some depend_fields are not enums (like object_class from CCompteRendu)
      if (!isset($object->_specs[$depend_field]->_list)) {
        continue;
      }
      foreach ($object->_specs[$depend_field]->_list as $value) {
        $locale = "$class.$depend_field.$value";
        $list[$value] = $locale;
        $listTraductions[$locale] = CAppUI::tr($locale);
      }
    }
  }
}

CMbArray::removeValue(array(), $classes);

$userSel = CMediusers::get($filter_user_id);
$userSel->loadRefFunction()->loadRefGroup();

$filter_user_id = $userSel->_id;

$listPrat = $userSel->loadUsers(PERM_EDIT);
$listFunc = CMediusers::loadFonctions(PERM_EDIT);
$listEtab = CGroups::loadGroups(PERM_EDIT);

// Aide sélectionnée
$aide = new CAideSaisie();
$aide->load($aide_id);

// Accès aux aides à la saisie de la fonction et de l'établissement
$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_group");

if ($aide->_id) {
  if ($aide->function_id && !$access_function) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  if ($aide->group_id && !$access_group) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  $aide->loadRefUser();
  $aide->loadRefFunction();
  $aide->loadRefGroup();
}
else {
  $aide->user_id = $userSel->user_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"         , $userSel);
$smarty->assign("listPrat"        , $listPrat);
$smarty->assign("listFunc"        , $listFunc);
$smarty->assign("listEtab"        , $listEtab);
$smarty->assign("access_function" , $access_function);
$smarty->assign("access_group"    , $access_group);
$smarty->assign("classes"         , $classes);
$smarty->assign("aide"            , $aide);
$smarty->assign("start"           , $start);
$smarty->assign("keywords"        , $keywords);
$smarty->assign("filter_class"    , $filter_class);
$smarty->assign("filter_user_id"  , $filter_user_id);
$smarty->assign("listTraductions" , $listTraductions);
$smarty->assign("order_col_aide"  , $order_col_aide);
$smarty->assign("order_way"       , $order_way);
$smarty->assign("aide_id"         , $aide_id);

$smarty->display("vw_idx_aides.tpl");
