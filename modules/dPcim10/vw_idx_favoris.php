<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$user = $AppUI->user_id;

$lang = CValue::getOrSession("lang", CCodeCIM10::LANG_FR);

// Recherche des codes favoris

$favoris = new CFavoricim10();
$where = array();
$where["favoris_user"] = "= '$AppUI->user_id'";
$order = "favoris_code";
$favoris = $favoris->loadList($where, $order);

$codes = array();
foreach($favoris as $key => $value) {
  $codes[$value->favoris_code] = new CCodeCIM10($value->favoris_code);
  $codes[$value->favoris_code]->loadLite($lang, 0);
  $codes[$value->favoris_code]->_favoris_id = $value->favoris_id;
  $codes[$value->favoris_code]->occ = "0";
}

// Chargement des favoris calculés
$ds = CSQLDataSource::get("std");
$sql = "SELECT DP, count(DP) as nb_code
        FROM `sejour`
        WHERE sejour.praticien_id = '$AppUI->user_id'
        AND DP IS NOT NULL
        AND DP != ''
        GROUP BY DP
        ORDER BY count(DP) DESC
        LIMIT 10;";
$cimStat = $ds->loadlist($sql);
 
$listCimStat = array();
foreach($cimStat as $key => $value) {
  $listCimStat[$value["DP"]] = new CCodeCIM10($value["DP"]);
  $listCimStat[$value["DP"]]->loadLite();
  $listCimStat[$value["DP"]]->_favoris_id = "0";
  $listCimStat[$value["DP"]]->occ = $value["nb_code"];
}

// Fusion des deux tableaux de favoris
$fusionCim = $listCimStat;
  
foreach($codes as $keycode => $code){
	if(!array_key_exists($keycode, $fusionCim)) {
		$fusionCim[$keycode] = $code;
		continue;
	}
}
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("lang" , $lang);
$smarty->assign("cim10", new CCodeCIM10);
$smarty->assign("fusionCim", $fusionCim);

$smarty->display("vw_idx_favoris.tpl");

?>