<?php

/**
 * dPsalleOp
 *  
 * @category dPsalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$object_id    = CValue::post("object_id");
$object_class = CValue::post("object_class");
$chir_id      = CValue::post("chir_id");
$anesth_id    = CValue::post("anesth_id");
$password_activite_1 = CValue::post("password_activite_1");
$password_activite_4 = CValue::post("password_activite_4");

$object = new $object_class;
$object->load($object_id);

if ($password_activite_1) {
  $chir = new CMediusers;
  $chir->load($chir_id);
  
  $user = new CUser;
  $user->user_username = $chir->_user_username;
  $user->_user_password = $password_activite_1;
  $user->loadMatchingObject();
  
  if (!$user->_id) {
    CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR );
    echo CAppUI::getMsg();
    CApp::rip();
  }
  
  $object->cloture_activite_1 = 1;
  
  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg(CAppUI::tr("COperation-msg-modify"), UI_MSG_OK);
  }
}

if ($password_activite_4) {
  $anesth = new CMediusers;
  $anesth->load($anesth_id);
  
  if ($anesth->_id) {
    $user = new CUser;
    $user->user_username = $anesth->_user_username;
    $user->_user_password = $password_activite_4;
    $user->loadMatchingObject();
    
    if (!$user->_id) {
      CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR );
      
      echo CAppUI::getMsg();
      CApp::rip();
    }
    
    $object->cloture_activite_4 = 1;
    
    if ($msg = $object->store()) {
      CAppUI::setMsg($sg, UI_MSG_ERROR);
    }
    else {
      CAppUI::setMsg(CAppUI::tr("COperation-msg-modify"), UI_MSG_OK);
    }
  }
}

// Transmission des actes CCAM
if (CAppUI::conf("dPpmsi transmission_actes") == "signature" && $object instanceof COperation && 
    $object->testCloture()) {
  $object->loadRefs();
  
  $actes_ccam = $object->_ref_actes_ccam;
  
  foreach ($object->_ref_actes_ccam as $acte_ccam) {
    $acte_ccam->loadRefsFwd();
  }
  
  $sejour = $object->_ref_sejour;
  $sejour->loadRefsFwd();
  $sejour->loadNDA();
  $sejour->_ref_patient->loadIPP();

  // Facturation de l'op�ration
  $object->facture = 1;
  $object->loadLastLog();
  
  try {
    $object->store();
  } catch(CMbException $e) {
    // Cas d'erreur on repasse � 0 la facturation
    $object->facture = 0;
    $object->store();
    
    CAppUI::setMsg($e->getMessage(), UI_MSG_ERROR );
  }
  
  $object->countExchanges();
  
  // Flag les actes CCAM en envoy�s
  foreach ($actes_ccam as $key => $_acte_ccam){
    $_acte_ccam->sent = 1;
    if ($msg = $_acte_ccam->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR );
    }
  }
}

echo CAppUI::getMsg();
CApp::rip();

?>