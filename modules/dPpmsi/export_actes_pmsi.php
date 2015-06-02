<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $m, $tab;

$module = CValue::get("module");
if (!$module) {
  $module = $m;
}

$canUnlockActes = $module == "dPpmsi" || CModule::getCanDo("dPsalleOp")->admin;

if (null == $object_class = CValue::get("object_class")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "$tab-msg-mode-missing");
  return;
}

$unlock_dossier = CValue::get("unlock_dossier", 0);

$NDA = "";
$IPP = "";

switch ($object_class) {
  case "COperation":
    $object = new COperation();

    // Chargement de l'opération et génération du document
    $operation_id = CValue::post("mb_operation_id", CValue::getOrSession("object_id"));
    if ($object->load($operation_id)) {
      $object->loadRefs();
      $codes = explode("|", $object->codes_ccam);
      $actes = CMbArray::pluck($object->_ref_actes_ccam, "code_acte");

      foreach ($object->_ref_actes_ccam as $acte_ccam) {
        $acte_ccam->loadRefsFwd();
      }

      // Suppression des actes non codés
      if (CAppUI::conf("dPsalleOp CActeCCAM del_actes_non_cotes")) {
        foreach ($codes as $_key => $_code) {
          $key = array_search($_code, $actes);
          if ($key === false) {
            unset($codes[$_key]);
          }
        }
      }
      $object->_codes_ccam = $codes;

      $mbSejour =& $object->_ref_sejour;
      $mbSejour->loadRefsFwd();
      $mbSejour->loadNDA();
      $NDA = $mbSejour->_NDA;
      $mbSejour->_ref_patient->loadIPP();
      $IPP = $mbSejour->_ref_patient->_IPP;
      if (isset($_POST["sc_patient_id"  ])) {
        $mbSejour->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
      }
      if (isset($_POST["sc_venue_id"    ])) {
        $mbSejour->_NDA               = $_POST["sc_venue_id"    ];
      }
      if (isset($_POST["cmca_uf_code"   ])) {
        $object->code_uf              = $_POST["cmca_uf_code"   ];
      }
      if (isset($_POST["cmca_uf_libelle"])) {
        $object->libelle_uf           = $_POST["cmca_uf_libelle"];
      }
    }
    break;

  case "CSejour":
    $object = new CSejour();

    // Chargement du séjour et génération du document
    $sejour_id = CValue::post("mb_sejour_id", CValue::getOrSession("object_id"));
    if ($object->load($sejour_id)) {
      $object->loadRefs();
      $object->loadRefDossierMedical();
      $object->loadNDA();
      $NDA = $object->_NDA;
      $object->_ref_patient->loadIPP();
      $IPP = $object->_ref_patient->_IPP;
      if (isset($_POST["sc_patient_id"  ])) {
        $object->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
      }
      if (isset($_POST["sc_venue_id"    ])) {
        $object->_NDA               = $_POST["sc_venue_id"    ];
      }
    }
    break;
}

// Facturation de l'opération où du séjour
$object->facture     = 1;
if ($unlock_dossier) {
  $object->facture = 0;
}
else {
  $object->_force_sent = true;
}

$object->loadLastLog();

try {
  $object->store();
}
catch(CMbException $e) {
  // Cas d'erreur on repasse la facturation à l'état précédent
  $object->facture = 0;
  if ($unlock_dossier) {
    $object->facture = 1;
  }
  $object->store();

  $e->stepAjax();
}

$object->countExchanges("pmsi", "evenementServeurActe");

if (!$unlock_dossier) {
  // Flag les actes CCAM en envoyés
  foreach ($object->_ref_actes_ccam as $_acte_ccam) {
    $_acte_ccam->sent = 1;
    if ($msg = $_acte_ccam->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR );
    }
  }
}

$order = "date_production DESC";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("canUnlockActes", $canUnlockActes);
$smarty->assign("object", $object);
$smarty->assign("IPP", $IPP);
$smarty->assign("NDA", $NDA);
$smarty->assign("module", $module);
$smarty->display("../../dPpmsi/templates/inc_export_actes_pmsi.tpl");
