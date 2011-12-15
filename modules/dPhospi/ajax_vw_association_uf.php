<?php

CCanDo::checkEdit();

// Récupération des paramètres
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();
$curr_affectation_guid = CValue::get("curr_affectation_guid");
$lit_guid = CValue::get("lit_guid");

$lit = CMbObject::loadFromGuid($lit_guid);
$chambre = $lit->loadRefChambre();
$service = $chambre->loadRefService();

$affectation = CMbObject::loadFromGuid($curr_affectation_guid);
$affectation->loadRefSejour();
$affectation->loadRefUfs();
$praticien = $affectation->_ref_sejour->loadRefPraticien();

$uf  = new CAffectationUniteFonctionnelle();

$choixmedical    = array();
$choixsoins      = array();
$choixhebergment = array();

$where = array();
$where["object_class"] = " = 'CService'";
$where["object_id"]    = " = '$service->_id'";
$services   = $uf->loadList($where);
foreach($services as $serv){
	$serv->loadRefUniteFonctionnelle();
	$choixsoins[$serv->uf_id]=$serv;
	$choixhebergment[$serv->uf_id]=$serv;
}

$where["object_class"] = " = 'CChambre'";
$where["object_id"]    = " = '$chambre->_id'";
$chambres   = $uf->loadList($where);
foreach($chambres as $ch){
  $ch->loadRefUniteFonctionnelle();
  $choixhebergment[$ch->uf_id]=$ch;
}

$where["object_class"] = " = 'CLit'";
$where["object_id"]    = " = '$lit->_id'";
$lits       = $uf->loadList($where);
foreach($lits as $_lit){
  $_lit->loadRefUniteFonctionnelle();
  $choixhebergment[$_lit->uf_id]=$_lit;
}

$where["object_class"] = " = 'CFunctions'";
$where["object_id"]    = " = '{$praticien->_ref_function->_id}'";
$fonctions  = $uf->loadList($where);
foreach($fonctions as $fct){
  $fct->loadRefUniteFonctionnelle();
  $choixmedical[$fct->uf_id]=$fct;
}

$where["object_class"] = " = 'CMediusers'";
$where["object_id"]    = " = '{$praticien->_id}'";
$mediusers  = $uf->loadList($where);
foreach($mediusers as $med){
  $med->loadRefUniteFonctionnelle();
  $choixmedical[$med->uf_id]=$med;
}

$choixmedical     = array_reverse ($choixmedical);
$choixsoins       = array_reverse ($choixsoins);
$choixhebergment  = array_reverse ($choixhebergment);

$nomservice = $service->nom;
$hebergement = array($nomservice => $services, 
                     $chambre->nom => $chambres,
                     $lit->nom => $lits);
$medical     = array("{$praticien->_ref_function->text}" => $fonctions,
                     "{$praticien->_view}" => $mediusers);
    

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectation" , $affectation);
$smarty->assign("services"    , $services);
$smarty->assign("nomservice"  , $nomservice);
$smarty->assign("hebergement" , $hebergement);
$smarty->assign("medical"     , $medical);

$smarty->assign("choixmedical"    , $choixmedical);
$smarty->assign("choixsoins"      , $choixsoins);
$smarty->assign("choixhebergment" , $choixhebergment);

$smarty->display("inc_vw_affectation_uf.tpl");
?>