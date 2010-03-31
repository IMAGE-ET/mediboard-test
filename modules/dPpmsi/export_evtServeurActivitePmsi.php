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
		$evenementPMSI = new CHPrimXMLServeurActes();

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
		}
		break;
  case "sej" :
		$mbObject = new CSejour();
		$evenementPMSI = new CHPrimXMLEvenementPmsi();
				
		// Chargement du séjour et génération du document
		$sejour_id = CValue::post("mb_sejour_id", CValue::getOrSession("object_id"));
		if ($mbObject->load($sejour_id)) {
		  $mbObject->loadRefs();
		  $mbObject->loadRefDossierMedical();
		  $mbObject->loadNumDossier();
		  $mbObject->_ref_patient->loadIPP();
		  if (isset($_POST["sc_patient_id"  ])) $mbObject->_ref_patient->_IPP = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $mbObject->_num_dossier       = $_POST["sc_venue_id"    ];
		}
    break;
}

if (!$evenementPMSI->checkSchema()) {
  return;
}
			
$dest_hprim = new CDestinataireHprim();
$dest_hprim->group_id = CGroups::loadCurrent()->_id;
$dest_hprim->evenement = "pmsi";
$destinataires = $dest_hprim->loadMatchingList();
        
foreach ($destinataires as $_destinataire) {
	$evenementPMSI->emetteur     = CAppUI::conf('mb_id');
  $evenementPMSI->destinataire = $_destinataire->nom;
  $evenementPMSI->group_id     = $_destinataire->group_id;

	$msgEvtPMSI = $evenementPMSI->generateTypeEvenement($mbObject);
	
	$echange_hprim = new CEchangeHprim();
  $echange_hprim->load($evenementPMSI->identifiant);
  if ($doc_valid = $echange_hprim->message_valide) {
    $mbObject->facture = true;
    $mbObject->store();
  }
		
	$logs = array();
	if ($_destinataire->actif) {
    $source = CExchangeSource::get($_destinataire->_guid);
		$sent_files = CValue::get("sent_files");
		if (isset($_POST["hostname"]) or ($doc_valid and !$sent_files)) {
		  $source->setData($msgEvtPMSI);
		  if ($source->send()) {
        $echange_hprim->date_echange = mbDateTime();
        $echange_hprim->store();
		    $logs[] = "Archivage du fichier envoyé sur le serveur Mediboard";
		  }
		}
  }
}

$order = "date_production DESC";
// Récupération de tous les échanges produits
$mbObject->loadBackRefs("echanges_hprim", $order);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenementPMSI"  , $evenementPMSI);
$smarty->assign("exchange_source", $source);
$smarty->assign("logs"           , $logs);
$smarty->assign("doc_valid"      , @$doc_valid);
$smarty->assign("typeObject"     , $typeObject);
$smarty->assign("mbObject"       , $mbObject);
$smarty->display("export_evtServeurActivitePmsi.tpl");

?>