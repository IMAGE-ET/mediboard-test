<?php

/**
 * Liste d'aides à la saisie
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDO::checkRead();

// Utilisateur sélectionné ou utilisateur courant
$user_id        = CValue::getOrSession("user_id");
$function_id    = CValue::getOrSession("function_id");
$class          = CValue::getOrSession("class");
$start          = CValue::getOrSession("start");
$keywords       = CValue::getOrSession("keywords");
$order_col_aide = CValue::getOrSession("order_col_aide");
$order_way      = CValue::getOrSession("order_way");
$aide_id        = CValue::getOrSession("aide_id", "0");

$order_by = $order_col_aide ? $order_col_aide . " " . $order_way : null;

$userSel = CMediusers::get($user_id);
$userSel->loadRefFunction()->loadRefGroup();

$function = new CFunctions();
$function->load($function_id);
$function->loadRefGroup();

$where = array();
if ($class) {
  $where["class"] = "= '$class'";
}

// Liste des aides pour le praticien

// Accès aux aides à la saisie de la fonction et de l'établissement
$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_group");

$aides      = array();
$aidesCount = array();

if (!$function_id) {
  $aides["user"]      = array();
  $aidesCount["user"] = 0;
}
if ($access_function) {
  $aides["func"]      = array();
  $aidesCount["func"] = 0;
}
if ($access_group) {
  $aides["etab"]      = array();
  $aidesCount["etab"] = 0;
}

$_aide = new CAideSaisie();

foreach ($aides as $owner => $_aides_by_owner) {
  switch ($owner) {
    case "user":
      $key_where = "user_id";
      $where[$key_where]   = "= '$userSel->user_id'";

      break;
    case "func":
      $key_where = "function_id";
      $where[$key_where] = "= '".($function_id ? $function_id : $userSel->function_id) ."'";

      break;
    case "etab":
      $key_where = "group_id";
      $where[$key_where]  = "= '".($function_id ? $function->_ref_group->_id  : $userSel->_ref_function->group_id) ."'";

      break;
  }

  $aides["{$owner}_ids"] = array_keys($_aide->seek($keywords, $where, 1000));
  $aides[$owner] = $_aide->seek($keywords, $where, $start[$owner].", 30", true, null, $order_by);
  $aidesCount[$owner] = $_aide->_totalSeek;
  unset($where[$key_where]);

  foreach ($aides[$owner] as $aide) {
    $aide->loadRefUser();
    $aide->loadRefFunction();
    $aide->loadRefGroup();
  }

  CStoredObject::massLoadBackRefs($aides[$owner], 'hypertext_links');
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"       , $userSel);
$smarty->assign("function"      , $function);
$smarty->assign("aides"         , $aides);
$smarty->assign("aidesCount"    , $aidesCount);
$smarty->assign("class"         , $class);
$smarty->assign("start"         , $start);
$smarty->assign("order_col_aide", $order_col_aide);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("aide_id"       , $aide_id);

$smarty->display("inc_tabs_aides.tpl");
