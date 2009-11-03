<?php

$object_class    = CValue::getOrSession("object_class");
$object_id       = CValue::getOrSession("object_id");
$module          = CValue::getOrSession("module");
$do_subject_aed  = CValue::getOrSession("do_subject_aed");
$chir_id         = CValue::getOrSession("chir_id");

$date  = CValue::getOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;


// Chargement de la liste des praticiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

// Chargement de la liste des anesthesistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$codable = new $object_class;
$codable->load($object_id);

$codable->loadRefPatient();
$codable->loadRefPraticien();
$codable->getAssociationCodesActes();
$codable->loadExtCodesCCAM();
$codable->loadPossibleActes();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesths"      , $listAnesths);
$smarty->assign("listChirs"        , $listChirs);
$smarty->assign("subject"          , $codable);
$smarty->assign("module"           , $module);
$smarty->assign("do_subject_aed"   , $do_subject_aed);
$smarty->assign("chir_id"          , $chir_id); 
$smarty->assign("modif_operation"  , $modif_operation);
$smarty->display("inc_codage_ccam.tpl");

?>