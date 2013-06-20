<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_id    = CValue::post("object_id");
$object_class = CValue::post("object_class");
$chir_id      = CValue::post("chir_id");
$anesth_id    = CValue::post("anesth_id");
$password_activite_1 = CValue::post("password_activite_1");
$password_activite_4 = CValue::post("password_activite_4");

/** @var COperation|CSejour $object */
$object = new $object_class;
$object->load($object_id);

if ($password_activite_1) {
  $chir = new CMediusers;
  $chir->load($chir_id);

  if (!CUser::checkPassword($chir->_user_username, $password_activite_1)) {
    CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR);
    echo CAppUI::getMsg();
    CApp::rip();
  }

  $object->cloture_activite_1 = 1;

  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg("COperation-msg-modify", UI_MSG_OK);
  }
}

if ($password_activite_4) {
  $anesth = new CMediusers;
  $anesth->load($anesth_id);

  if ($anesth->_id) {
    if (!CUser::checkPassword($anesth->_user_username, $password_activite_4)) {
      CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR);

      echo CAppUI::getMsg();
      CApp::rip();
    }

    $object->cloture_activite_4 = 1;

    if ($msg = $object->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
    else {
      CAppUI::setMsg("COperation-msg-modify", UI_MSG_OK);
    }
  }
}

// Transmission des actes CCAM
if (CAppUI::conf("dPpmsi transmission_actes") == "signature" && $object instanceof COperation && $object->testCloture()) {
  $object->loadRefs();

  $actes_ccam = $object->_ref_actes_ccam;

  foreach ($object->_ref_actes_ccam as $acte_ccam) {
    $acte_ccam->loadRefsFwd();
  }

  $sejour = $object->_ref_sejour;
  $sejour->loadRefsFwd();
  $sejour->loadNDA();
  $sejour->_ref_patient->loadIPP();

  // Facturation de l'opération
  $object->facture = 1;
  $object->loadLastLog();

  try {
    $object->store();
  } catch(CMbException $e) {
    // Cas d'erreur on repasse à 0 la facturation
    $object->facture = 0;
    $object->store();

    CAppUI::setMsg($e->getMessage(), UI_MSG_ERROR );
  }

  $object->countExchanges();

  // Flag les actes CCAM en envoyés
  foreach ($actes_ccam as $key => $_acte_ccam) {
    $_acte_ccam->sent = 1;
    if ($msg = $_acte_ccam->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
  }
}

echo CAppUI::getMsg();
CApp::rip();
