<?php

/**
 * dPcim10
 *  
 * @category dPcim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$chir           = CValue::get("chir");
$anesth         = CValue::get("anesth");
$_keywords_code = CValue::get("_keywords_code");
$_all_codes     = CValue::get("_all_codes", 0);
$object_class   = CValue::get("object_class");
$only_list      = CValue::get("only_list", 0);
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
  
  // Statistiques
  $ds = CSQLDataSource::get("std");
  $sql = "SELECT DP, count(DP) as nb_code
          FROM `sejour`
          WHERE sejour.praticien_id = '$_user_id'
          AND DP IS NOT NULL
          AND DP != ''
          GROUP BY DP
          ORDER BY count(DP) DESC
          LIMIT 10;";
  
  $cimStat = $ds->loadlist($sql);
   
  $codes_stats = array();
  foreach($cimStat as $key => $value) {
    $codes_stats[$value["DP"]] = new CCodeCIM10($value["DP"]);
    $codes_stats[$value["DP"]]->loadLite();
    $codes_stats[$value["DP"]]->_favoris_id = "0";
    $codes_stats[$value["DP"]]->occ = $value["nb_code"];
  }
  
  // Favoris
  $code = new CFavoriCIM10;
  $where = array();
  $where["favoris_user"] = " = '$_user_id'";
  $codes_favoris = $code->loadList($where, null, 100);
  
  foreach ($codes_favoris as $key => $_code) {
    $codes_favoris[$_code->favoris_code] = $_code;
    unset($codes_favoris[$key]);
  }
  
  // Seek sur les codes, avec ou non l'inclusion de tous les codes
  $code = new CCodeCIM10();
  $where = null;

  if (!$_all_codes && (count($codes_stats) || count($codes_favoris))) {
    $where = "master.abbrev IN (";
    
    foreach ($codes_stats as $key => $_code) {
      $where .= "'" . $key . "'";
      if ($_code != end($codes_stats)) {
        $where .= ",";
      }
    }
    
    if (count($codes_stats) && count($codes_favoris)) {
      $where .= ",";
    }
    
    foreach ($codes_favoris as $key => $_code) {
      $where .= "'" . $key . "'";
      if ($_code != end($codes_favoris)) {
        $where .= ",";
      }
    }
    $where .= ")";
  }
  
  // Si pas de stat et pas de favoris, et que la recherche se fait sur ceux-ci,
  // alors le tableau de résultat est vide
  if (!$_all_codes && count($codes_stats) == 0 && count($codes_favoris) == 0) {
    $codes = array();
  }
  // Sinon recherche de codes
  else {
    $codes = $code->findCodes($_keywords_code, $_keywords_code, CCodeCIM10::LANG_FR, null, $where);
  }
  
  foreach($codes as $key=>$value) {
    $val_code = $value["code"];
    $code_cim10 = new CCodeCIM10;
    $code_cim10->CCodeCIM10($val_code, 1);
    $list[$val_code] = $code_cim10;
    $nb_code = 0;
    if (isset($codes_stats[$val_code])) {
      $nb_code = $codes_stats[$val_code]->occ;
    }
    elseif (isset($codes_favoris[$val_code])) {
      $nb_code = 0.5;
    }
    $list[$val_code]->nb_acte = $nb_code;
  }
  
  $sorter = CMbArray::pluck($list, "nb_acte");
  
  array_multisort($sorter, SORT_DESC, $list);
  
  $listByProfile[$profile]["favoris"] = $codes_favoris;
  $listByProfile[$profile]["stats"]   = $codes_stats;
  $listByProfile[$profile]["list"]    = $list;
}

$smarty = new CSmartyDP;

$smarty->assign("listByProfile", $listByProfile);
$smarty->assign("users"        , $users);
$smarty->assign("object_class" , $object_class);
$smarty->assign("_keywords_code", $_keywords_code);
$smarty->assign("_all_codes"   , $_all_codes);

if ($only_list) {
  $smarty->display("inc_code_selector_cim10.tpl");
}
else {
  $smarty->assign("chir"         , $chir);
  $smarty->assign("anesth"       , $anesth);
  $smarty->display("code_selector_cim10.tpl");
}