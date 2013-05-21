<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$chir           = CValue::get("chir");
$anesth         = CValue::get("anesth");
$_keywords_code = CValue::get("_keywords_code");
$_all_codes     = CValue::get("_all_codes", 0);
$object_class   = CValue::get("object_class");
$only_list      = CValue::get("only_list", 0);
$tag_id         = CValue::get("tag_id");

$user   = CUser::get();
$ds     = CSQLDataSource::get("std");
$profiles = array (
  "chir"   => $chir,
  "anesth" => $anesth,
  "user"   => $user->_id,
);

if ($profiles["user"] == $profiles["anesth"] || $profiles["user"] == $profiles["chir"]) {
  unset($profiles["user"]);
}

if (!$profiles["anesth"]) {
  unset($profiles["anesth"]);
}

$listByProfile = array();
$users = array();

foreach ($profiles as $profile => $_user_id) {
  $keywords_code = $_keywords_code;
  $_user = new CMediusers();
  $_user->load($_user_id);
  $users[$profile] = $_user;
  $list = array();
  $codes_stats = array();

  if (!$tag_id) {
    // Statistiques
    $actes = new CActeCCAM;
    $codes_stats = $actes->getFavoris($_user_id, $object_class);

    foreach ($codes_stats as $key => $_code) {
      $codes_stats[$_code["code_acte"]] = $_code;
      unset($codes_stats[$key]);
    }
  }

  // Favoris

  $code = new CFavoriCCAM();
  $where = array();
  $where["ccamfavoris.favoris_user"] = " = '$_user_id'";
  $where["ccamfavoris.object_class"] = " = '$object_class'";

  $ljoin = array();
  if ($tag_id) {
    $where["tag_item.tag_id"] = "= '$tag_id'";
    $ljoin["tag_item"] = "tag_item.object_id = ccamfavoris.favoris_id AND tag_item.object_class = 'CFavoriCCAM'";
  }

  /** @var CFavoriCCAM[] $codes_favoris */
  $codes_favoris = $code->loadList($where, null, 100, null, $ljoin);

  foreach ($codes_favoris as $key => $_code) {
    $codes_favoris[$_code->favoris_code] = $_code;
    unset($codes_favoris[$key]);
  }

  // Seek sur les codes, avec ou non l'inclusion de tous les codes
  $code = new CCodeCCAM("");
  $where = null;

  if (!$_all_codes && (count($codes_stats) || count($codes_favoris))) {
    // Si on a la recherche par tag, on n'utilise pas les stats (les tags sont mis sur les favoris)
    if ($tag_id) {
      $codes_keys = array_keys($codes_favoris);
    }
    else {
      $codes_keys = array_keys(array_merge($codes_stats, $codes_favoris));
    }
    $where = "CODE ".$ds->prepareIn($codes_keys);
  }

  if (!$_all_codes && count($codes_stats) == 0 && count($codes_favoris) == 0) {
    // Si pas de stat et pas de favoris, et que la recherche se fait sur ceux-ci,
    // alors le tableau de résultat est vide
    $codes = array();
  }
  else {
    // Sinon recherche de codes
    $codes = $code->findCodes($_keywords_code, $_keywords_code, null, $where);
  }
  
  foreach ($codes as $value) {
    $val_code = $value["CODE"];
    $list[$val_code] = CCodeCCAM::get($val_code, CCodeCCAM::MEDIUM);
    $nb_acte = 0;
    if (isset($codes_stats[$val_code])) {
      $nb_acte = $codes_stats[$val_code]["nb_acte"];
    }
    else if (isset($codes_favoris[$val_code])) {
      $nb_acte = 0.5;
    }
    $list[$val_code]->nb_acte = $nb_acte;
  }
  
  if ($tag_id) {
    $sorter = CMbArray::pluck($list, "code");
    array_multisort($sorter, SORT_ASC, $list);
  }
  else {
    $sorter = CMbArray::pluck($list, "nb_acte");
    array_multisort($sorter, SORT_DESC, $list);
  }

  $listByProfile[$profile]["favoris"] = $codes_favoris;
  $listByProfile[$profile]["stats"]   = $codes_stats;
  $listByProfile[$profile]["list"]    = $list;
}

$tag_tree = CFavoriCCAM::getTree($user->_id);

$smarty = new CSmartyDP;

$smarty->assign("listByProfile" , $listByProfile);
$smarty->assign("users"         , $users);
$smarty->assign("object_class"  , $object_class);
$smarty->assign("_keywords_code", $_keywords_code);
$smarty->assign("_all_codes"    , $_all_codes);
$smarty->assign("tag_tree"      , $tag_tree);
$smarty->assign("tag_id"        , $tag_id);

if ($only_list) {
  $smarty->display("inc_code_selector_ccam.tpl");
}
else {
  $smarty->assign("chir"         , $chir);
  $smarty->assign("anesth"       , $anesth);
  $smarty->display("code_selector_ccam.tpl");
}
