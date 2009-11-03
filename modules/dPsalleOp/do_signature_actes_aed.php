<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/


function viewMsg($msg, $action, $redirect = "", $txt = ""){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
    $AppUI->redirect($redirect);
    return;
  }
  $AppUI->setMsg("$action $txt", UI_MSG_OK );
}


global $AppUI, $m;

$date = CValue::post("date");
$dialog = CValue::post("dialog");
$praticien_id = CValue::post("praticien_id");
$object_id = CValue::post("object_id");
$object_class = CValue::post("object_class");
$password = CValue::post("password");

if($dialog){
  $redirectUrl = "m=dPsalleOp&a=vw_signature_actes&object_id=$object_id&object_class=$object_class&dialog=1";
} else {
  $redirectUrl = "m=dPsalleOp&tab=vw_signature_actes&date=$date";
}

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Test du password en chargeant 
$user = new CUser();
$user->user_username = $praticien->_user_username;
$user->_user_password = $password;

if(!$user->_user_password) {
  viewMsg("Veuillez saisir votre mot de passe", "Signature des actes", $redirectUrl);
}

$user->loadMatchingObject();

if(!$user->_id){
  viewMsg("Mot de passe incorrect", "Signature des actes", $redirectUrl);
}

// Chargement des actes CCAM à modifier
$acte_ccam = new CActeCCAM();
$acte_ccam->object_id = $object_id;
$acte_ccam->object_class = $object_class;
$acte_ccam->executant_id = $user->_id;
$acte_ccam->signe = 0;
$actes_ccam = $acte_ccam->loadMatchingList();

// Modification des actes CCAM
foreach($actes_ccam as $key => $_acte_ccam){
  $_acte_ccam->signe = 1;
  $msg = $_acte_ccam->store();
  viewMsg($msg, "CActeCCAM-title-modify", $redirectUrl);
}

$AppUI->redirect($redirectUrl);

?>