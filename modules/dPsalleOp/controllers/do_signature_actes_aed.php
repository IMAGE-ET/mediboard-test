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

if (!$user->_user_password) {
  CAppUI::setMsg("Veuillez saisir votre mot de passe", UI_MSG_ERROR );
  if ($redirectUrl) {
    CAppUI::redirect($redirectUrl);
  }
  CApp::rip();
}

$user->loadMatchingObject();

if (!$user->_id){
  CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR );
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

// Transmission des actes CCAM lors de la signature
if (CAppUI::conf("dPpmsi transmission_actes") == "signature" && $object_class == "COperation") {  
  $mbObject = new COperation();
  // Chargement de l'opération et génération du document
  if ($mbObject->load($object_id)) {
    $mbObject->loadRefs();
    foreach ($mbObject->_ref_actes_ccam as $acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
    $mbSejour =& $mbObject->_ref_sejour;
    $mbSejour->loadRefsFwd();
    $mbSejour->loadNDA();
    $mbSejour->_ref_patient->loadIPP();
  }
  
  if (!$evenementActivitePMSI->checkSchema()) {
    return;
  }
        
  // Facturation de l'opération où du séjour
  $mbObject->facture = 1;
  $mbObject->loadLastLog();
  try {
    $mbObject->store();
  } catch(CMbException $e) {
    // Cas d'erreur on repasse à 0 la facturation
    $mbObject->facture = 0;
    $mbObject->store();
    
    $e->stepAjax();
  }
  
  $mbObject->countExchanges();
  
  // Flag les actes CCAM en envoyés
  foreach ($actes_ccam as $key => $_acte_ccam){
    $_acte_ccam->sent = 1;
    if ($msg = $_acte_ccam->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR );
    }
  }
}

if ($redirectUrl) {
  CAppUI::redirect($redirectUrl);
}

CApp::rip();

?>