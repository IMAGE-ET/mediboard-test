<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Sébastien Fillonneau
*/

// Récupération des variables
$objectClass  = CValue::get("objectClass"  , null);
$objectId     = CValue::get("objectId"     , null);
$elementClass = CValue::get("elementClass" , null);
$elementId    = CValue::get("elementId"    , null);
$popup        = CValue::get("popup"        , 0);
$nonavig      = CValue::get("nonavig"      , null);
$sfn          = CValue::get("sfn"          , 0);
$typeVue      = CValue::getOrSession("typeVue");


// Déclaration de variables
$file_id          = null;
$object           = null;
$fileSel          = null;
$keyFileSel       = null;
$page_prev        = null;
$page_next        = null;
$pageEnCours      = null;
$includeInfosFile = null;
$catFileSel       = null;
$acces_denied     = true;      // droit d'affichage du fichier demandé
$arrNumPages      = array();   // navigation par pages (PDF)
$isConverted      = false;
$display_as_is    = false;

$pdf_active = (CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 1 ) &&
  (CAppUI::pref("pdf_and_thumbs") == 1);

// Création du template
$smarty = new CSmartyDP();

if ($objectClass && $objectId && $elementClass && $elementId) {
  // Chargement de l'objet
  $object = new $objectClass;

  if ($object->load($objectId)) {
    // Chargement des fichiers et des Documents
    $object->loadRefsFiles();
    $object->loadRefsDocs();
    
    // Recherche du fichier/document demandé et Vérification droit Read
    if ($elementClass == "CFile") {
      $type = "_ref_files";
      $nameFile = "file_name";
    }
    
    if ($elementClass == "CCompteRendu") {
      $type = "_ref_documents";
      $nameFile = "nom";
    }

    if (array_key_exists($elementId, $object->$type)) {
      $listFile =& $object->$type;
      $listFile[$elementId]->canRead();
      $acces_denied = !$listFile[$elementId]->_canRead;

      if ($listFile[$elementId]->_canRead) {
        $fileSel = $listFile[$elementId];
        $file_id = $fileSel->_id;

        if ($pdf_active && $type == "_ref_documents") {
          $compte_rendu = new CCompteRendu;
          $compte_rendu->load($elementId);
          $compte_rendu->loadFile();
          $fileSel = $compte_rendu->_ref_file;
          $file_id = $fileSel->_id;
        }

        $keyTable = $listFile[$elementId]->_spec->key;
        $keyFileSel = $listFile[$elementId]->$nameFile;
        $keyFileSel .= "-" . $elementClass . "-";
        $keyFileSel .= $listFile[$elementId]->$keyTable;
        // Récupération de la catégorie
        $catFileSel = new CFilesCategory;
        $catFileSel->load($fileSel->file_category_id);
      }
    }
    else {
      CAppUI::stepAjax(CAppUI::tr("CDocumentItem-_not_available"), UI_MSG_WARNING);
      CApp::rip();
    }
  }
  else {
    // Objet Inexistant
    $object = null;
  }
}

$show_editor = true;

// Gestion des pages pour les Fichiers PDF et fichiers TXT
if ($fileSel && $elementClass == "CFile" && !$acces_denied) {
  if (file_exists($fileSel->_file_path)) {
    $raw_content = file_get_contents($fileSel->_file_path);

    switch ($fileSel->file_type) {
      case "application/x-hprim":
        $includeInfosFile = CHprim21::formatHPRIMBiologie($raw_content);
        $display_as_is    = true;
        $show_editor      = false;
        break;

      case "text/osoft":
        if (class_exists("COsoftHistorique")) {
          $osoft_histo      = new COsoftHistorique;
          $includeInfosFile = $osoft_histo->toHTML($raw_content);
          $show_editor      = false;
          break;
        }

      case "application/osoft":
        if (class_exists("COsoftDossier")) {
          $osoft_dossier    = new COsoftDossier;
          $includeInfosFile = $osoft_dossier->toHTML($raw_content);
          $show_editor      = false;
          break;
        }

      case "text/plain": 
        $includeInfosFile = "<pre>".CMbString::htmlSpecialChars($raw_content)."</pre>";
        break;

      case "text/html":
        $includeInfosFile = CMbString::purifyHTML($raw_content);
        $show_editor      = false;
        $display_as_is    = true;
        break;
    }
  }

  if ($fileSel->isPDFconvertible()) {
    $isConverted = true;
    $fileconvert = $fileSel->loadPDFconverted();
    $success = 1;

    if (!$fileconvert->_id) {
      $success = $fileSel->convertToPDF();
      
    }
    if ($success == 1) {
      $fileconvert = $fileSel->loadPDFconverted();
      $fileconvert->loadNbPages();
      $fileSel->_nb_pages = $fileconvert->_nb_pages;
    }
  }
  
  if (!$fileSel->_nb_pages) {
    $fileSel->loadNbPages();
  }
  
  if ($fileSel->_nb_pages) {
    if ($sfn > $fileSel->_nb_pages || $sfn < 0) {
      $sfn = 0;
    }

    if ($sfn != 0) {
      $page_prev = $sfn - 1; 
    }
    if ($sfn < ($fileSel->_nb_pages-1)) {
      $page_next = $sfn + 1;
    }
    for ($i = 1; $i <= $fileSel->_nb_pages; $i++) {
      $arrNumPages[] = $i;
    }
  }
}
elseif ($fileSel && $elementClass == "CCompteRendu" && !$acces_denied && !$pdf_active) {
  $fileSel->loadContent();
  $includeInfosFile = $fileSel->_source;
}

if ($pdf_active && $elementClass == "CCompteRendu") {
  $fileSel->loadNbPages();

  if ($fileSel->_nb_pages) {
    if ($sfn>$fileSel->_nb_pages || $sfn<0) {
      $sfn = 0;
    }
    if ($sfn!=0) {
      $page_prev = $sfn - 1; 
    }
    if ($sfn<($fileSel->_nb_pages-1)) {
      $page_next = $sfn + 1;
    }
    for ($i=1;$i<=$fileSel->_nb_pages;$i++) {
      $arrNumPages[] = $i;
    }
  }
}
else {
  // Initialisation de FCKEditor
  if ($includeInfosFile) {
    $templateManager = new CTemplateManager;
    $templateManager->printMode = true;
    $templateManager->initHTMLArea();
  }
}


$smarty->assign("objectClass"     , $objectClass);
$smarty->assign("objectId"        , $objectId);
$smarty->assign("elementClass"    , $elementClass);
$smarty->assign("elementId"       , $elementId);
$smarty->assign("catFileSel"      , $catFileSel);
$smarty->assign("fileSel"         , $fileSel);
$smarty->assign("arrNumPages"     , $arrNumPages);
$smarty->assign("object"          , $object);
$smarty->assign("page_prev"       , $page_prev);
$smarty->assign("page_next"       , $page_next);
$smarty->assign("sfn"             , $sfn);
$smarty->assign("includeInfosFile", $includeInfosFile);    
$smarty->assign("popup"           , $popup);
$smarty->assign("acces_denied"    , $acces_denied);
$smarty->assign("file_id"         , $file_id);
$smarty->assign("isConverted"     , $isConverted);
$smarty->assign("show_editor"     , $show_editor);
$smarty->assign("display_as_is"   , $display_as_is);

if ($popup==1) {
  $listCat  = null;
  $fileprev = null;
  $filenext = null;

  if ($object) {
    $affichageFile = CFile::loadDocItemsByObject($object);
    
    // Récupération du fichier/doc préc et suivant
    $aAllFilesDocs = array();
    foreach ($affichageFile as $keyCat => $currCat) {
      $aAllFilesDocs = array_merge($aAllFilesDocs,$affichageFile[$keyCat]["items"]);
    }
        
    $aFilePrevNext = CMbArray::getPrevNextKeys($aAllFilesDocs, $keyFileSel);
    foreach ($aFilePrevNext as $key=>$value) {
      if ($value) {
        $aFile =& $aAllFilesDocs[$aFilePrevNext[$key]];
        $keyFile = $aFile->_spec->key;
        ${"file".$key} = array(
          "elementId"    => $aFile->$keyFile,
          "elementClass" => $aFile->_class,
        );
      }
    }
    
    // Récupération des destinataires pour l'envoi par mail
    $destinataires = array();
    CDestinataire::makeAllFor($object);
    $list_destinataires = CDestinataire::$destByClass;
    
    foreach ($list_destinataires as $_destinataires_by_class) {
      foreach ($_destinataires_by_class as $_destinataire) {
        if (!isset($_destinataire->nom) || strlen($_destinataire->nom) == 0 || $_destinataire->nom === " ") {
          continue;
        }
        $destinataires[] =
          array("nom"   => $_destinataire->nom,
                "email" => $_destinataire->email,
                "tag"   => $_destinataire->tag);
      }
    }
    
    $exchange_source = CExchangeSource::get("mediuser-".CAppUI::$user->_id, "smtp");
    $smarty->assign("exchange_source", $exchange_source);
    $smarty->assign("destinataires"  , $destinataires);
    $smarty->assign("nonavig"  , $nonavig);
    $smarty->assign("filePrev" , $fileprev);
    $smarty->assign("fileNext" , $filenext);
    $smarty->display("inc_preview_file_popup.tpl");
  }
}
else {
  $smarty->display("inc_preview_file.tpl");
}