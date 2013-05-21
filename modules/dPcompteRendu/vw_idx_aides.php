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
$order_col      = CValue::getOrSession("order_col", "class");
$order_way      = CValue::getOrSession("order_way", "ASC");

$classes = array_flip(CApp::getInstalledClasses());
$listTraductions = array();

// Chargement des champs d'aides a la saisie
foreach ($classes as $class => &$infos) {
  $listTraductions[$class] = CAppUI::tr($class);
  $object = new $class;
  $infos = array();
  foreach ($object->_specs as $field => $spec) {
    if (isset($spec->helped)) {
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
}

CMbArray::removeValue(array(), $classes);

// Liste des users accessibles
$listPrat = new CMediusers();
$listFct = $listPrat->loadFonctions(PERM_EDIT);

$where = array();
$where["users_mediboard.function_id"] = CSQLDataSource::prepareIn(array_keys($listFct));

$ljoin = array();
$ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";

$order = "`users`.`user_last_name`, `users`.`user_first_name`";

$listPrat = $listPrat->loadList($where, $order, null, null, $ljoin);

/** @var $mediuser CMediusers */
foreach ($listPrat as $keyUser => $mediuser) {
  $mediuser->_ref_function =& $listFct[$mediuser->function_id];
}

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$listEtab = CGroups::loadGroups(PERM_EDIT);

$userSel = CMediusers::get($filter_user_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefGroup();

if ($userSel->isPraticien()) {
  CValue::setSession("filter_user_id", $userSel->user_id);
  $filter_user_id = $userSel->user_id;
}

// Aide sélectionnée
$aide = new CAideSaisie();
$aide->load($aide_id); 
$aide->loadRefs();

if (!$aide_id) {
  $aide->user_id = $userSel->user_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"         , $userSel);
$smarty->assign("listPrat"        , $listPrat);
$smarty->assign("listFunc"        , $listFunc);
$smarty->assign("listEtab"        , $listEtab);
$smarty->assign("classes"         , $classes);
$smarty->assign("aide"            , $aide);
$smarty->assign("start"           , $start);
$smarty->assign("keywords"        , $keywords);
$smarty->assign("filter_class"    , $filter_class);
$smarty->assign("filter_user_id"  , $filter_user_id);
$smarty->assign("listTraductions" , $listTraductions);
$smarty->assign("order_col"       , $order_col);
$smarty->assign("order_way"       , $order_way);
$smarty->assign("aide_id"         , $aide_id);

$smarty->display("vw_idx_aides.tpl");
