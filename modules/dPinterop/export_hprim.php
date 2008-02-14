<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $dPconfig;

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

$can->needsRead();

$typeObject = mbGetValueFromGet("typeObject");

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
$sent_files = mbGetValueFromGet("sent_files");

switch($typeObject) {
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
		  if (isset($_POST["sc_patient_id"  ])) $mbSejour->_ref_patient->SHS = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $mbSejour->venue_SHS         = $_POST["sc_venue_id"    ];
		  if (isset($_POST["cmca_uf_code"   ])) $mbObject->code_uf           = $_POST["cmca_uf_code"   ];
		  if (isset($_POST["cmca_uf_libelle"])) $mbObject->libelle_uf        = $_POST["cmca_uf_libelle"];
		  if (!$doc->checkSchema()) {
		    return;
		  }
		  $doc->generateFromOperation($mbObject);
		  $doc_valid = $doc->schemaValidate();
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
		  if (isset($_POST["sc_patient_id"  ])) $mbObject->_ref_patient->SHS = $_POST["sc_patient_id"  ];
		  if (isset($_POST["sc_venue_id"    ])) $mbObject->venue_SHS         = $_POST["sc_venue_id"    ];
		  if (!$doc->checkSchema()) {
		    return;
		  }
		  $doc->generateFromSejour($mbObject);
		  $doc_valid = $doc->schemaValidate();
		}
    break;
}

// Nécessaire pour la validation avec XML Spy
//$doc->addNameSpaces();
$doc->saveTempFile();

// Connexion FTP
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