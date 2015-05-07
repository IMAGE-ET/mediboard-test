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

$filtre = new CAideSaisie();

// Utilisateur sélectionné ou utilisateur courant
$filtre->user_id     = CView::get("user_id", "num pos", true);
$filtre->function_id = CView::get("function_id", "num pos", true);
$filtre->class       = CView::get("class", "str" , true);
$keywords            = CView::get("keywords", "str", true);
$start               = CValue::getOrSession("start", array("user" => 0, "func" => 0, "etab" => 0));
$order_col_aide      = CView::get("order_col_aide", "enum list|class|field|depend_value_1|depend_value_2|name default|class", true);
$order_way           = CView::get("order_way", "enum list|ASC|DESC default|ASC", true);

CView::checkin();

$classes = array_flip(CApp::getInstalledClasses());

// Chargement des classes possibles pour les aides à la saisie
foreach ($classes as $class => &$infos) {
  $object = new $class;
  $infos = array();
  foreach ($object->_specs as $field => $spec) {
    if (!isset($spec->helped)) {
      continue;
    }
    $info =& $infos[$field];
    if (!is_array($spec->helped)) {
      $info = null;
    }
  }
}

CMbArray::removeValue(array(), $classes);

$filtre->loadRefUser();
$filtre->loadRefFunction();

$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_function");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filtre"          , $filtre);
$smarty->assign("access_function" , $access_function);
$smarty->assign("classes"         , $classes);
$smarty->assign("start"           , $start);
$smarty->assign("keywords"        , $keywords);
$smarty->assign("order_col_aide"  , $order_col_aide);
$smarty->assign("order_way"       , $order_way);

$smarty->display("vw_idx_aides.tpl");
