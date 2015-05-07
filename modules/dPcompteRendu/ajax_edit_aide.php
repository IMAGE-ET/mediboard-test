<?php 

/**
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$aide_id     = CValue::getOrSession("aide_id");
$user_id     = CValue::getOrSession("user_id");
$function_id =
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
}
else {
  $aide->user_id = $user_id;
}

$aide->loadRefUser();
$aide->loadRefFunction();
$aide->loadRefGroup();

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

$smarty = new CSmartyDP();

$smarty->assign("aide"            , $aide);
$smarty->assign("listTraductions" , $listTraductions);
$smarty->assign("classes"         , $classes);
$smarty->assign("access_function" , $access_function);
$smarty->assign("access_group"    , $access_group);

$smarty->display("inc_edit_aide.tpl");