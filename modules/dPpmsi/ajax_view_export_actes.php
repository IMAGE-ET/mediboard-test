<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage pmsi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

if (null == $object_class = CValue::get("object_class")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "$tab-msg-mode-missing");
  return;
}

$NDA = "";
$IPP = "";

$confirmCloture = CValue::get("confirmCloture", 0);

switch ($object_class) {
  case "COperation" :
    $object = new COperation();

    // Chargement de l'opération et génération du document
    $operation_id = CValue::post("mb_operation_id", CValue::getOrSession("object_id"));
    if ($object->load($operation_id)) {
      $object->loadRefSejour();

      $mbSejour = $object->_ref_sejour;
      $mbSejour->loadNDA();
      $NDA = $mbSejour->_NDA;
      $mbSejour->loadRefPatient();
      $mbSejour->_ref_patient->loadIPP();
      $IPP = $mbSejour->_ref_patient->_IPP;
    }
    break;
  case "CSejour" :
    $object = new CSejour();

    // Chargement du séjour et génération du document
    $sejour_id = CValue::post("mb_sejour_id", CValue::getOrSession("object_id"));
    if ($object->load($sejour_id)) {
      $object->loadRefPatient();
      $object->loadRefDossierMedical();
      $object->loadNDA();
      $NDA = $object->_NDA;
      $object->_ref_patient->loadIPP();
      $IPP = $object->_ref_patient->_IPP;
    }
    break;
}

$object->countExchanges("pmsi", "evenementServeurActe");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("IPP"   , $IPP);
$smarty->assign("NDA"   , $NDA);
$smarty->assign("confirmCloture", $confirmCloture);
$smarty->display("../../dPpmsi/templates/inc_export_actes_pmsi.tpl");
