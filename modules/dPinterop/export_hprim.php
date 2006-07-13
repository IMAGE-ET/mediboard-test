<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $dPconfig;

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass($m, "hprimxmlserveuractes"));
$mbOp = new COperation();
$doc = new CHPrimXMLServeurActes;

// Chargement de l'opération et génération du document
$mb_operation_id = dPgetParam($_POST, "mb_operation_id", mbGetValueFromGetOrSession("operation_id"));
if ($mbOp->load($mb_operation_id)) {
  $mbOp->loadRefs();

  foreach ($mbOp->_ref_actes_ccam as $acte_ccam) {
    $acte_ccam->loadRefsFwd();
  }

  $mbSejour =& $mbOp->_ref_sejour;
  $mbSejour->loadRefsFwd();
  
  if (isset($_POST["sc_patient_id"  ])) $mbSejour->_ref_patient->SHS = $_POST["sc_patient_id"  ];
  if (isset($_POST["sc_venue_id"    ])) $mbSejour->venue_SHS         = $_POST["sc_venue_id"    ];
  if (isset($_POST["cmca_uf_code"   ])) $mbOp->code_uf               = $_POST["cmca_uf_code"   ];
  if (isset($_POST["cmca_uf_libelle"])) $mbOp->libelle_uf            = $_POST["cmca_uf_libelle"];
  
  if (!$doc->checkSchema()) {
    return;
  }
  
  $doc->generateFromOperation($mbOp);
  $doc_valid = $doc->schemaValidate();
}

// Nécessaire pour la validation avec XML Spy
//$doc->addNameSpaces();

$doc->saveTempFile();

require_once($AppUI->getSystemClass("ftp"));

// HPRIM export FTP settings
$HPrimConfig = $dPconfig["dPinterop"]["hprim_export"];

$fileprefix = dPgetParam($_POST, "fileprefix", $HPrimConfig["fileprefix"]);

$ftp = new CFTP;
$ftp->hostname = dPgetParam($_POST, "hostname", $HPrimConfig["hostname"]);
$ftp->username = dPgetParam($_POST, "username", $HPrimConfig["username"]);
$ftp->userpass = dPgetParam($_POST, "userpass", $HPrimConfig["userpass"]);

$ajax = mbGetValueFromGet("ajax");
$sent_files = mbGetValueFromGet("sent_files");

// Connexion FTP
if (isset($_POST["hostname"]) or ($ajax and $doc_valid and !$sent_files)) {
  // Compte le nombre de fichiers déjà générés
  mbForceDirectory($doc->finalpath);
  $count = 0;
  $dir = dir($doc->finalpath);
  while (false !== ($entry = $dir->read())) {
    $count++;
  }
  $dir->close();
  $count -= 2; // Exclure . et ..
  $counter = $count % 100;
  
  // Transfert réel
  $destination_basename = sprintf("%s%02d", $fileprefix, $counter);
  // Transfert en mode FTP_ASCII obligatoire pour les AS400
  if ($ftp->sendFile($doc->documentfilename, "$destination_basename.xml", FTP_ASCII)) {
    $ftp->sendFile($doc->documentfilename, "$destination_basename.ok", FTP_ASCII);

    $doc->saveFinalFile();
    $documentFinalBaseName = basename($doc->documentfinalfilename);
    $ftp->logStep("Archivage du fichier envoyé sur le serveur Mediboard sous le nom $documentFinalBaseName");
  }
}

$doc->getSentFiles();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign("doc", $doc);
$smarty->assign("fileprefix", $fileprefix);
$smarty->assign("ftp", $ftp);
$smarty->assign("ajax", $ajax);
$smarty->assign("doc_valid", @$doc_valid);
$smarty->assign("mbOp", $mbOp);

$smarty->display("export_hprim.tpl");

?>