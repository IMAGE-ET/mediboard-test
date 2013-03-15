<?php

$object_class    = CValue::getOrSession("object_class");
$object_id       = CValue::getOrSession("object_id");
$module          = CValue::getOrSession("module");
$do_subject_aed  = CValue::getOrSession("do_subject_aed");
$chir_id         = CValue::getOrSession("chir_id");

$date  = CValue::getOrSession("date", CMbDT::date());


// Chargement de la liste des praticiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

// Chargement de la liste des anesthesistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$codable = new $object_class;
$codable->load($object_id);
$codable->isCoded();

$codable->loadRefPatient();
$codable->loadRefPraticien();
$codable->loadExtCodesCCAM();
$codable->getAssociationCodesActes();
$codable->loadPossibleActes();
$codable->canDo();
if($codable->_class == "COperation") {
  $codable->countExchanges();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesths"      , $listAnesths);
$smarty->assign("listChirs"        , $listChirs);
$smarty->assign("subject"          , $codable);
$smarty->assign("module"           , $module);
$smarty->assign("do_subject_aed"   , $do_subject_aed);
$smarty->assign("chir_id"          , $chir_id);
$smarty->display("inc_codage_ccam.tpl");
