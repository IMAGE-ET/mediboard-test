<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
// Récupération des paramètres
$curr_affectation_guid = CValue::get("curr_affectation_guid");
$uf_medicale  = CValue::get("uf_medicale_id");
$lit_guid     = CValue::get("lit_guid");
$lit = CMbObject::loadFromGuid($lit_guid);

$mediuser = CMediusers::get();
$mediuser->loadRefFunction();

/** @var CAffectation $affectation */
$affectation = CMbObject::loadFromGuid($curr_affectation_guid);
$affectation->loadRefUfs();
$sejour         = $affectation->loadRefSejour();
$praticien      = $sejour->loadRefPraticien();
$prat_placement = $affectation->loadRefPraticien();
$function       = $praticien->loadRefFunction();

if (!$prat_placement->_id) {
  $prat_placement = $praticien;
}

$user = new CMediusers();
$praticiens = array();

if ($uf_medicale) {
  $users = array();
  $function_med = array();

  $where = array();
  $where["affectation_uf.uf_id"] = "= '".$uf_medicale."'";
  $where[] = "object_class = 'CMediusers' OR object_class = 'CFunctions'";
  /* @var CAffectationUniteFonctionnelle[] $affs*/
  $aff_ufs = new CAffectationUniteFonctionnelle();
  $affs = $aff_ufs->loadList($where);
  foreach ($affs as $_aff) {
    if ($_aff->object_class == "CMediusers") {
      $users[$_aff->object_id] = $_aff->object_id;
    }
    else {
      $function_med[$_aff->object_id] = $_aff->object_id;
    }
  }

  $where = array();
  $where["actif"] = "= '1'";
  $where[] = "user_id ".CSQLDataSource::prepareIn(array_keys($users))."OR function_id ".CSQLDataSource::prepareIn(array_keys($function_med));
  $praticiens = $user->loadList($where);
}
else {
  $praticiens = $user->loadPraticiens(PERM_EDIT, $function->_id);
}

foreach ($praticiens as $prat) {
  $prat->loadRefFunction();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectation"   , $affectation);
$smarty->assign("lit"           , $lit);
$smarty->assign("praticien"     , $praticien);
$smarty->assign("prat_placement", $prat_placement);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("see_validate"  , CValue::get("see_validate", 1));

$smarty->display("inc_vw_select_prat_uf.tpl");
