<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;
$can->needsRead();

if (null == $typeObject = CValue::get("typeObject")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "$tab-msg-mode-missing");
  return;
}

switch ($typeObject) {
  case "op" :
		$mbObject = new COperation();
		$doc = new CHPrimXMLServeurActes();
		
		// Chargement de l'opération et génération du document
		$operation_id = CValue::post("mb_operation_id", CValue::getOrSession("object_id"));
		if ($mbObject->load($operation_id)) {
		  $mbObject->loadRefs();
		  foreach ($mbObject->_ref_actes_ccam as $acte_ccam) {
		    $acte_ccam->loadRefsFwd();
		  }
		  $mbSejour =& $mbObject->_ref_sejour;
		  $mbSejour->loadRefsFwd();
		  $mbSejour->loadNumDossier();
		  $mbSejour->_ref_patient->loadIPP();
		  if (isset($_POST["sc_patient_id"  ])) $mbSejour->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $mbSejour->_num_dossier       = $_POST["sc_venue_id"    ];
		  if (isset($_POST["cmca_uf_code"   ])) $mbObject->code_uf            = $_POST["cmca_uf_code"   ];
		  if (isset($_POST["cmca_uf_libelle"])) $mbObject->libelle_uf         = $_POST["cmca_uf_libelle"];
		  if (!$doc->checkSchema()) {
		    return;
		  }
		  $doc->generateFromOperation($mbObject);
			if ($doc_valid = $doc->schemaValidate()) {
			  $mbObject->facture = true;
				$mbObject->store();
			}
		}
		break;
  case "sej" :
		$mbObject = new CSejour();
		$doc = new CHPrimXMLEvenementPmsi();
				
		// Chargement du séjour et génération du document
		$sejour_id = CValue::post("mb_sejour_id", CValue::getOrSession("object_id"));
		if ($mbObject->load($sejour_id)) {
		  $mbObject->loadRefs();
		  $mbObject->loadRefDossierMedical();
		  $mbObject->loadNumDossier();
		  $mbObject->_ref_patient->loadIPP();
		  if (isset($_POST["sc_patient_id"  ])) $mbObject->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $mbObject->_num_dossier       = $_POST["sc_venue_id"    ];
		  if (!$doc->checkSchema()) {
		    return;
		  }
		  $doc->generateFromSejour($mbObject);
		  $doc_valid = $doc->schemaValidate();
			if ($doc_valid = $doc->schemaValidate()) {
        $mbObject->facture = true;
        $mbObject->store();
      }
		}
    break;
}

// Traitement sur le document HPRIM produit
//$doc->addNameSpaces(); 	// Nécessaire pour la validation avec XML Spy
$doc->saveTempFile();

// Envoi à la source créée 'PMSI'
$exchange_source = CExchangeSource::get("pmsi");

$sent_files = CValue::get("sent_files");
if (isset($_POST["hostname"]) or ($doc_valid and !$sent_files)) {
  $exchange_source->setData($doc->saveXML());
  $logs = array();
  if ($exchange_source->send()) {
    $doc->saveFinalFile();
    $documentFinalBaseName = basename($doc->documentfinalfilename);
    $logs[] = "Archivage du fichier envoyé sur le serveur Mediboard sous le nom $documentFinalBaseName";
  }
}

// Récupération de tous les fichiers produits
$doc->getSentFiles();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("doc"            , $doc);
$smarty->assign("exchange_source", $exchange_source);
$smarty->assign("logs"           , $logs);
$smarty->assign("doc_valid"      , @$doc_valid);
$smarty->assign("typeObject"     , $typeObject);
$smarty->assign("mbObject"       , $mbObject);

$smarty->display("export_evtServeurActivitePmsi.tpl");

?>