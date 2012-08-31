<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/

$date = CValue::post("date");
$dialog = CValue::post("dialog");
$praticien_id = CValue::post("praticien_id");
$object_id = CValue::post("object_id");
$object_class = CValue::post("object_class");
$password = CValue::post("password");

if ($dialog){
  $redirectUrl = null;
}
else {
  $redirectUrl = "m=dPsalleOp&tab=vw_signature_actes&date=$date";
}

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Test du password
if (!$password) {
  CAppUI::setMsg("Veuillez saisir votre mot de passe", UI_MSG_ERROR);
  
  echo CAppUI::getMsg();
  
  if ($redirectUrl) {
    CAppUI::redirect($redirectUrl);
  }
  
  CApp::rip();
}

if (!CUser::checkPassword($praticien->_user_username, $password)){
  CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR);
  
  echo CAppUI::getMsg();
  
  if ($redirectUrl) {
    CAppUI::redirect($redirectUrl);
  }
  
  CApp::rip();
}

// Chargement des actes CCAM à modifier
$acte_ccam = new CActeCCAM();
$acte_ccam->object_id = $object_id;
$acte_ccam->object_class = $object_class;
$acte_ccam->executant_id = $user->_id;
$acte_ccam->signe = 0;
$actes_ccam = $acte_ccam->loadMatchingList();

// Modification des actes CCAM
foreach ($actes_ccam as $key => $_acte_ccam){
  $_acte_ccam->signe = 1;
  $msg = $_acte_ccam->store();
  if ($msg) {
    CAppUI::setMsg($msg, UI_MSG_ERROR );
  }
}

if ($redirectUrl) {
  CAppUI::redirect($redirectUrl);
}

CApp::rip();

?>