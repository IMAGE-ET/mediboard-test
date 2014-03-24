<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

// Récupération des paramètres
$callback = CValue::get("callback");

$mediuser = CMediusers::get();
$mediuser->loadRefFunction();
$curr_affectation_guid = CValue::get("curr_affectation_guid");
$lit_guid = CValue::get("lit_guid");

$lit = CMbObject::loadFromGuid($lit_guid);
$chambre = $lit->loadRefChambre();
$service = $chambre->loadRefService();

/** @var CAffectation $affectation */
$affectation = CMbObject::loadFromGuid($curr_affectation_guid);
$affectation->loadRefUfs();
$sejour         = $affectation->loadRefSejour();
$praticien      = $sejour->loadRefPraticien();
$prat_placement = $affectation->loadRefPraticien();
$function       = $praticien->loadRefFunction();

$ufs_medicale    = array();
$ufs_soins       = array();
$ufs_hebergement = array();
$uf_sejour_hebergement = array();
$uf_sejour_medicale = array();
$uf_sejour_soins = array();

$auf = new CAffectationUniteFonctionnelle();

// UFs de séjour
$ufs_sejour = array();

$uf = $sejour->loadRefUFHebergement();
if ($uf->_id) {
  $uf_sejour_hebergement[$uf->_id] = $uf;
  $ufs_hebergement[$uf->_id] = $uf;
}

$uf = $sejour->loadRefUFMedicale();
if ($uf->_id) {
  $uf_sejour_medicale[$uf->_id] = $uf;
  $ufs_medicale[$uf->_id] = $uf;
}

$uf = $sejour->loadRefUFSoins();
if ($uf->_id) {
  $uf_sejour_soins[$uf->_id] = $uf;
  $ufs_soins[$uf->_id] = $uf;
}

// UFs de services
$ufs_service = array();
foreach ($auf->loadListFor($service) as $_auf) {
  $uf = $_auf->loadRefUniteFonctionnelle();
  $ufs_service    [$uf->_id] = $uf;
  $ufs_soins      [$uf->_id] = $uf;
  $ufs_hebergement[$uf->_id] = $uf;
}

// UFs de chambre
$ufs_chambre = array();
foreach ($auf->loadListFor($chambre) as $_auf) {
  $uf = $_auf->loadRefUniteFonctionnelle();
  $ufs_chambre    [$uf->_id] = $uf;
  $ufs_hebergement[$uf->_id] = $uf;
}

// UFs de lit
$ufs_lit = array();
foreach ($auf->loadListFor($lit) as $_auf) {
  $uf = $_auf->loadRefUniteFonctionnelle();
  $ufs_lit        [$uf->_id] = $uf;
  $ufs_hebergement[$uf->_id] = $uf;
}

// UFs de fonction
$ufs_function = array();
foreach ($auf->loadListFor($function) as $_auf) {
  $uf = $_auf->loadRefUniteFonctionnelle();
  $ufs_function   [$uf->_id] = $uf;
  $ufs_medicale   [$uf->_id] = $uf;
}

// UFs de praticien
$ufs_praticien_sejour = array();
$ufs_prat_placement = array();
foreach ($auf->loadListFor($praticien) as $_auf) {
  $uf = $_auf->loadRefUniteFonctionnelle();
  $ufs_praticien_sejour [$uf->_id] = $uf;
  $ufs_medicale  [$uf->_id] = $uf;
}

if ($prat_placement->_id) {
  foreach ($auf->loadListFor($prat_placement) as $_auf) {
    $uf = $_auf->loadRefUniteFonctionnelle();
    $ufs_prat_placement [$uf->_id] = $uf;
    $ufs_medicale  [$uf->_id] = $uf;
  }
}
else {
  $prat_placement = $praticien;
}

$user = new CMediusers();
$praticiens = array();
if ($affectation->_ref_uf_medicale->_id) {
  $users = array();
  $function_med = array();

  $where = array();
  $where["affectation_uf.uf_id"] = "= '".$affectation->_ref_uf_medicale->_id."'";
  $where[] = "object_class = 'CMediusers' OR object_class = 'CFunctions'";
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
  foreach ($auf->loadListFor($prat) as $_auf) {
    $uf = $_auf->loadRefUniteFonctionnelle();
    $ufs_medicale[$uf->_id] = $uf;
  }
}

$ufs_medicale    = array_reverse($ufs_medicale);
$ufs_soins       = array_reverse($ufs_soins);
$ufs_hebergement = array_reverse($ufs_hebergement);    

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectation", $affectation);
$smarty->assign("sejour"     , $sejour);
$smarty->assign("service"    , $service);
$smarty->assign("chambre"    , $chambre);
$smarty->assign("lit"        , $lit);
$smarty->assign("function"   , $function);
$smarty->assign("praticien"  , $praticien);
$smarty->assign("prat_placement" , $prat_placement);
$smarty->assign("praticiens" , $praticiens);

$smarty->assign("uf_sejour_hebergement", $uf_sejour_hebergement);
$smarty->assign("uf_sejour_soins", $uf_sejour_soins);
$smarty->assign("uf_sejour_medicale", $uf_sejour_medicale);
$smarty->assign("ufs_service"    , $ufs_service);
$smarty->assign("ufs_chambre"    , $ufs_chambre);
$smarty->assign("ufs_lit"        , $ufs_lit);
$smarty->assign("ufs_function"   , $ufs_function);
$smarty->assign("ufs_praticien_sejour"  , $ufs_praticien_sejour);
$smarty->assign("ufs_prat_placement"    , $ufs_prat_placement);
$smarty->assign("ufs_medicale"   , $ufs_medicale);
$smarty->assign("ufs_soins"      , $ufs_soins);
$smarty->assign("ufs_hebergement", $ufs_hebergement);

$smarty->assign("see_validate", CValue::get("see_validate", 1));
$smarty->assign("callback", $callback);

$smarty->display("inc_vw_affectation_uf.tpl");
