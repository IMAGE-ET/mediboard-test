<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

global $can, $m, $dPconfig;
$can->needsRead();

// HPRIM export FTP settings
$HPrimConfig = $dPconfig["dPinterop"]["hprim_export"];
$fileprefix    = dPgetParam($_POST, "fileprefix", $HPrimConfig["fileprefix"]);
$filenbroll    = dPgetParam($_POST, "filenbroll", $HPrimConfig["filenbroll"]);
$fileextension = dPgetParam($_POST, "fileextension", $HPrimConfig["fileextension"]);

$ftp = new CFTP;
$ftp->hostname = dPgetParam($_POST, "hostname", $HPrimConfig["hostname"]);
$ftp->username = dPgetParam($_POST, "username", $HPrimConfig["username"]);
$ftp->userpass = dPgetParam($_POST, "userpass", $HPrimConfig["userpass"]);

$ajax = mbGetValueFromGet("ajax");

if (null == $typeObject = mbGetValueFromGet("typeObject")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "$tab-msg-mode-missing");
  return;
}

switch ($typeObject) {
  case "op" :
		$mbObject = new COperation();
		$doc = new CHPrimXMLServeurActes();
		
		// Chargement de l'opération et génération du document
		$mb_operation_id = dPgetParam($_POST, "mb_operation_id", mbGetValueFromGetOrSession("object_id"));
		if ($mbObject->load($mb_operation_id)) {
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
				$mbSejour->facture = true;
				$mbSejour->store();
			}
		}
		break;
  case "sej" :
		$mbObject = new CSejour();
		$doc = new CHPrimXMLEvenementPmsi();
				
		// Chargement du séjour et génération du document
		$mb_sejour_id = dPgetParam($_POST, "mb_sejour_id", mbGetValueFromGetOrSession("object_id"));
		if ($mbObject->load($mb_sejour_id)) {
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

// Connexion FTP
 $sent_files = mbGetValueFromGet("sent_files");
if (isset($_POST["hostname"]) or ($ajax and $doc_valid and !$sent_files)) {
  // Compte le nombre de fichiers déjà générés
  CMbPath::forceDir($doc->finalpath);
  $count = 0;
  $dir = dir($doc->finalpath);
  while (false !== ($entry = $dir->read())) {
    $count++;
  }
  $dir->close();
  $count -= 2; // Exclure . et ..
  $counter = $count % pow(10, $filenbroll);
  
  // Transfert réel
  $destination_basename = sprintf("%s%0".$filenbroll."d", $fileprefix, $counter);
  // Transfert en mode FTP_ASCII obligatoire pour les AS400
  if($ftp->connect()) {
    $ftp->sendFile($doc->documentfilename, "$destination_basename.$fileextension", FTP_ASCII);
    $ftp->sendFile($doc->documentfilename, "$destination_basename.ok", FTP_ASCII);

    $doc->saveFinalFile();
    $documentFinalBaseName = basename($doc->documentfinalfilename);
    $ftp->logStep("Archivage du fichier envoyé sur le serveur Mediboard sous le nom $documentFinalBaseName");
    $ftp->close();
  }
}

// Récupération de tous les fichiers produits
$doc->getSentFiles();
		
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("doc"       , $doc);
$smarty->assign("fileprefix", $fileprefix);
$smarty->assign("ftp"       , $ftp);
$smarty->assign("ajax"      , $ajax);
$smarty->assign("doc_valid" , @$doc_valid);
$smarty->assign("typeObject", $typeObject);
$smarty->assign("mbObject"  , $mbObject);

$smarty->display("export_hprim.tpl");

?>