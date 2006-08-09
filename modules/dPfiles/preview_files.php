<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPfiles", "files"));
require_once($AppUI->getModuleClass("dPfiles", "filescategory"));

$file_id = mbGetValueFromGet("file_id", 0);
$sfn = mbGetValueFromGet("sfn", 0);
$popup = mbGetValueFromGet("popup", 0);
$navig = mbGetValueFromGet("navig", null);

$largeur = null;
$hauteur = null;

$page_prev = null;
$page_next = null;
$pageEnCours = null;
$includeInfosFile = null;

$file = new CFile;
$file->load($file_id);
$file->loadNbPages();  
if($file->file_type == "text/plain"){
  // Fichier texte, on récupére le contenu
  $includeInfosFile = nl2br(utf8_decode(file_get_contents($file->_file_path)));
}

//navigation par pages (PDF)
if($file->_nb_pages){
  if($sfn>$file->_nb_pages || $sfn<0){$sfn = 0;}
  if($sfn!=0){
    $page_prev = $sfn - 1; 
  }
  if($sfn<($file->_nb_pages-1)){
    $page_next = $sfn + 1;
  }
  $pageEnCours = "Page ".($sfn+1)." / $file->_nb_pages";
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("file_id"         , $file_id     );
$smarty->assign("file"            , $file        );
$smarty->assign("page_prev"       , $page_prev   );
$smarty->assign("page_next"       , $page_next   );
$smarty->assign("sfn"             , $sfn         );
$smarty->assign("pageEnCours"     , $pageEnCours );
$smarty->assign("includeInfosFile", $includeInfosFile);    

if($popup==1){
    //Récupération de la liste des fichiers
    $listFiles = new CFile;
    $where = array();
    $where["file_class"] = "='$file->file_class'";
    $where["file_object_id"] = "= '$file->file_object_id'";
    $order = "nom,file_date";
    $leftjoin = array();
    $leftjoin["files_category"] = "files_mediboard.file_category_id=files_category.file_category_id";
    $listFiles = $listFiles->loadList($where, $order, null, null, $leftjoin);
    // Récupération du fichier précédent et suivant
    $filePrev = 0;
    $fileNext = 0;
    $en_cours = 0;
    foreach($listFiles as $keyFile => $dataFile){
      if($keyFile == $file_id){
        $filePrev = $en_cours;
      }
      if($en_cours == $file_id){
        $fileNext = $keyFile;
      }  
      $en_cours = $keyFile;
    }
    
    //Récupération de la catégorie du fichier en cours
    $listCat = new CFilesCategory;
    $listCat->load($file->file_category_id);
    
    $smarty->assign("filePrev" , $filePrev);
    $smarty->assign("fileNext" , $fileNext);
    $smarty->assign("listCat"  , $listCat);
      
    $smarty->display("inc_preview_file_popup.tpl");
}else{
  $smarty->display("inc_preview_file.tpl");
}
?>
