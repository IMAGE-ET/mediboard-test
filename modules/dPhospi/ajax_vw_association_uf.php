<?php

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

$affectation = CMbObject::loadFromGuid($curr_affectation_guid);
$affectation->loadRefUfs();
$sejour = $affectation->loadRefSejour();
$praticien = $sejour->loadRefPraticien();
$function = $praticien->loadRefFunction();

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
$ufs_praticien = array();
foreach ($auf->loadListFor($praticien) as $_auf) {
  $uf = $_auf->loadRefUniteFonctionnelle();
  $ufs_praticien [$uf->_id] = $uf;
  $ufs_medicale  [$uf->_id] = $uf;
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

$smarty->assign("uf_sejour_hebergement", $uf_sejour_hebergement);
$smarty->assign("uf_sejour_soins", $uf_sejour_soins);
$smarty->assign("uf_sejour_medicale", $uf_sejour_medicale);
$smarty->assign("ufs_service"    , $ufs_service);
$smarty->assign("ufs_chambre"    , $ufs_chambre);
$smarty->assign("ufs_lit"        , $ufs_lit);
$smarty->assign("ufs_function"   , $ufs_function);
$smarty->assign("ufs_praticien"  , $ufs_praticien);
$smarty->assign("ufs_medicale"   , $ufs_medicale);
$smarty->assign("ufs_soins"      , $ufs_soins);
$smarty->assign("ufs_hebergement", $ufs_hebergement);

$smarty->assign("see_validate", CValue::get("see_validate", 1));
$smarty->assign("callback", $callback);

$smarty->display("inc_vw_affectation_uf.tpl");
?>