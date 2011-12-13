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
$affectation->loadView();
$uf  = new CAffectationUniteFonctionnelle();

$choixmedical = array();
$choixsoins=array();
$choixhebergment=array();

$services   = $uf->loadList("object_class = 'CService' AND object_id= '$service->_id'" );
foreach($services as $serv){
	$serv->loadRefUniteFonctionnelle();
	$choixsoins[$serv->uf_id]=$serv;
	$choixhebergment[$serv->uf_id]=$serv;
}

$chambres   = $uf->loadList("object_class = 'CChambre' AND object_id = '$chambre->_id'");
foreach($chambres as $ch){
  $ch->loadRefUniteFonctionnelle();
  $choixhebergment[$ch->uf_id]=$ch;
}

$lits       = $uf->loadList("object_class = 'CLit' AND object_id = '$lit->_id'");
foreach($lits as $_lit){
  $_lit->loadRefUniteFonctionnelle();
  $choixhebergment[$_lit->uf_id]=$_lit;
}

$fonctions  = $uf->loadList("object_class = 'CFunctions' AND object_id = '{$mediuser->_ref_function->_id}'");
foreach($fonctions as $fct){
  $fct->loadRefUniteFonctionnelle();
  $choixmedical[$fct->uf_id]=$fct;
}

$mediusers  = $uf->loadList("object_class = 'CMediusers' AND object_id = '$mediuser->_id'");
foreach($mediusers as $med){
  $med->loadRefUniteFonctionnelle();
  $choixmedical[$med->uf_id]=$med;
}

$choixmedical     = array_reverse ($choixmedical);
$choixsoins       = array_reverse ($choixsoins);
$choixhebergment  = array_reverse ($choixhebergment);

$nomservice = $service->nom;
$hebergement = array("$service->nom" => $services, " $chambre->nom" => $chambres, "$lit->nom" => $lits);
//mbTrace($hebergement);
$medical     = array("{$mediuser->_ref_function->type}" => $fonctions, "$mediuser->_user_last_name " => $mediusers);
    

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectation" , $affectation);

$smarty->assign("services"    , $services);
$smarty->assign("nomservice"    , $nomservice);
$smarty->assign("hebergement" , $hebergement);
$smarty->assign("medical"     , $medical);

$smarty->assign("choixmedical"   , $choixmedical);
$smarty->assign("choixsoins"   , $choixsoins);
$smarty->assign("choixhebergment"   , $choixhebergment);

$smarty->display("inc_vw_affectation_uf.tpl");
?>