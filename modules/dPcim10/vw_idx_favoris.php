<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$user = CUser::get();

$lang   = CValue::getOrSession("lang", CCodeCIM10::LANG_FR);
$tag_id = CValue::getOrSession("tag_id");

// Recherche des codes favoris
$favoris = new CFavoriCIM10();
$where = array();
$where["favoris_user"] = "= '$user->_id'";

$ljoin = array();
if ($tag_id) {
  $ljoin["tag_item"] = "tag_item.object_id = favoris_id AND tag_item.object_class = 'CFavoriCIM10'";
  $where["tag_item.tag_id"] = "= '$tag_id'";
}

$favoris = $favoris->loadList($where, "favoris_code", null, null, $ljoin);

$codes = array();
foreach ($favoris as $_favori) {
  $favoris_code = $_favori->favoris_code;

  $_favori->loadRefsTagItems();

  $code = new CCodeCIM10($favoris_code);
  $code->loadLite($lang, 0);
  $code->_favoris_id = $_favori->favoris_id;
  $code->_ref_favori = $_favori;
  $code->occ = "0";

  $codes[$favoris_code] = $code;
}

// Chargement des favoris calculés
$ds = CSQLDataSource::get("std");
$sql = "SELECT DP, count(DP) as nb_code
        FROM `sejour`
        WHERE sejour.praticien_id = '$user->_id'
        AND DP IS NOT NULL
        AND DP != ''
        GROUP BY DP
        ORDER BY count(DP) DESC
        LIMIT 10;";
$cimStat = $ds->loadlist($sql);
 
$listCimStat = array();
foreach ($cimStat as $value) {
  $DP = $value["DP"];

  $code = new CCodeCIM10($DP);
  $code->loadLite();
  $code->_favoris_id = "0";
  $code->occ = $value["nb_code"];

  $listCimStat[$DP] = $code;
}

// Fusion des deux tableaux de favoris
$fusionCim = $listCimStat;
  
foreach ($codes as $keycode => $code) {
  if (!array_key_exists($keycode, $fusionCim)) {
    $fusionCim[$keycode] = $code;
    continue;
  }
}

$tag_tree = CFavoriCIM10::getTree($user->_id);
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("lang" , $lang);
$smarty->assign("cim10", new CCodeCIM10);
$smarty->assign("fusionCim", $fusionCim);
$smarty->assign("tag_tree", $tag_tree);
$smarty->assign("tag_id", $tag_id);

$smarty->display("vw_idx_favoris.tpl");
