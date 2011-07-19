<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

if (null == $object_class = CValue::get("object_class")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "$tab-msg-mode-missing");
  return;
}

switch ($object_class) {
  case "COperation" :
		$object = new COperation();
		
		// Chargement de l'opération et génération du document
		$operation_id = CValue::post("mb_operation_id", CValue::getOrSession("object_id"));
		if ($object->load($operation_id)) {
		  $object->loadRefs();
		  foreach ($object->_ref_actes_ccam as $acte_ccam) {
		    $acte_ccam->loadRefsFwd();
		  }
		  $mbSejour =& $object->_ref_sejour;
		  $mbSejour->loadRefsFwd();
		  $mbSejour->loadNumDossier();
		  $mbSejour->_ref_patient->loadIPP();
		  if (isset($_POST["sc_patient_id"  ])) $mbSejour->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $mbSejour->_num_dossier       = $_POST["sc_venue_id"    ];
		  if (isset($_POST["cmca_uf_code"   ])) $object->code_uf            = $_POST["cmca_uf_code"   ];
		  if (isset($_POST["cmca_uf_libelle"])) $object->libelle_uf         = $_POST["cmca_uf_libelle"];
		}
		break;
  case "CSejour" :
		$object = new CSejour();
				
		// Chargement du séjour et génération du document
		$sejour_id = CValue::post("mb_sejour_id", CValue::getOrSession("object_id"));
		if ($object->load($sejour_id)) {
		  $object->loadRefs();
		  $object->loadRefDossierMedical();
		  $object->loadNumDossier();
		  $object->_ref_patient->loadIPP();
		  if (isset($_POST["sc_patient_id"  ])) $object->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $object->_num_dossier       = $_POST["sc_venue_id"    ];
		}
    break;
}

// Facturation de l'opération où du séjour
$object->facture = 1;
$object->loadLastLog();
try {
  $object->store();
} catch(CMbException $e) {
  // Cas d'erreur on repasse à 0 la facturation
  $object->facture = 0;
  $object->store();
  
  $e->stepAjax();
}

$object->countExchanges();

// Flag les actes CCAM en envoyés
foreach ($object->_ref_actes_ccam as $_acte_ccam) {
  $_acte_ccam->sent = 1;
  if ($msg = $_acte_ccam->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR );
  }
}

$order = "date_production DESC";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("../../dPpmsi/templates/inc_export_actes_pmsi.tpl");

?>