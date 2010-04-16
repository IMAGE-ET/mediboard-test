<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

// R�cup�ration des variables
$objectClass  = CValue::get("objectClass"  , null);
$objectId     = CValue::get("objectId"     , null);
$elementClass = CValue::get("elementClass" , null);
$elementId    = CValue::get("elementId"    , null);
$popup        = CValue::get("popup"        , 0);
$nonavig      = CValue::get("nonavig"      , null);
$sfn          = CValue::get("sfn"          , 0);

// D�claration de variables
$object           = null;
$fileSel          = null;
$keyFileSel       = null;
$page_prev        = null;
$page_next        = null;
$pageEnCours      = null;
$includeInfosFile = null;
$catFileSel       = null;
$acces_denied     = true;      // droit d'affichage du fichier demand�
$arrNumPages      = array();   // navigation par pages (PDF)

$pdf_active = CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 1;

// Cr�ation du template
$smarty = new CSmartyDP();

if($objectClass && $objectId && $elementClass && $elementId){
  
  // Chargement de l'objet
  $object = new $objectClass;
  if($object->load($objectId)){

    // Chargement des fichiers et des Documents
    $object->loadRefsFiles();
    $object->loadRefsDocs();
    
    // Recherche du fichier/document demand� et V�rification droit Read
    if($elementClass == "CFile"){
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
      if($listFile[$elementId]->_canRead) {
        $fileSel = $listFile[$elementId];
        if($pdf_active && $type == "_ref_documents") {
          $compte_rendu = new CCompteRendu;
          $compte_rendu->load($elementId);
          $compte_rendu->loadFile();
          $fileSel = $compte_rendu->_ref_file;
        }
        $keyTable = $listFile[$elementId]->_spec->key;
        $keyFileSel = $listFile[$elementId]->$nameFile;
        $keyFileSel .= "-" . $elementClass . "-";
        $keyFileSel .= $listFile[$elementId]->$keyTable;
        // R�cup�ration de la cat�gorie
        $catFileSel = new CFilesCategory;
        $catFileSel->load($fileSel->file_category_id);
      }
    }
    
  }else {
  	// Objet Inexistant
    $object = null;
  }
}

// Gestion des pages pour les Fichiers PDF et fichiers TXT
if($fileSel && !$pdf_active && !$acces_denied){

  if($fileSel->file_type == "text/plain" && file_exists($fileSel->_file_path)){
    // Fichier texte, on r�cup�re le contenu
    $includeInfosFile = nl2br(htmlspecialchars(utf8_decode(file_get_contents($fileSel->_file_path))));
  }
  
  $fileSel->loadNbPages();
  if($fileSel->_nb_pages){
    if($sfn>$fileSel->_nb_pages || $sfn<0){$sfn = 0;}
    if($sfn!=0){
      $page_prev = $sfn - 1; 
    }
    if($sfn<($fileSel->_nb_pages-1)){
      $page_next = $sfn + 1;
    }
    for($i=1;$i<=$fileSel->_nb_pages;$i++){
      $arrNumPages[] = $i;
    }
  }
}

if ($pdf_active) {
  if ($elementClass == "CCompteRendu") {
    $elementId = $fileSel->_id;
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

if($popup==1){
  $listCat  = null;
  $fileprev = null;
  $filenext = null;
  if($object){	
    $affichageFile = CFile::loadDocItemsByObject($object);
    
    // R�cup�ration du fichier/doc pr�c et suivant
    $aAllFilesDocs = array();
    foreach($affichageFile as $keyCat => $currCat){
      $aAllFilesDocs = array_merge($aAllFilesDocs,$affichageFile[$keyCat]["items"]);
    }
        
    $aFilePrevNext = CMbArray::getPrevNextKeys($aAllFilesDocs, $keyFileSel);
    foreach($aFilePrevNext as $key=>$value){
      if($value){
        $aFile =& $aAllFilesDocs[$aFilePrevNext[$key]];
        $keyFile = $aFile->_spec->key;
        ${"file".$key} = array();
        ${"file".$key}["elementId"]   = $aFile->$keyFile;
        ${"file".$key}["elementClass"] = $aFile->_class_name;
      }
    }
    
    $smarty->assign("nonavig"  , $nonavig);
    $smarty->assign("filePrev" , $fileprev);
    $smarty->assign("fileNext" , $filenext);
    $smarty->display("inc_preview_file_popup.tpl");
  }
}else{
  $smarty->display("inc_preview_file.tpl");
}
?>