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

$file_id = mbGetValueFromGetOrSession("file_id", null);
$popup = mbGetValueFromGet("popup", 0);

$largeur = null;
$hauteur = null;

$file = new CFile;
$file->load($file_id);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("file_id", $file_id);
$smarty->assign("file"   , $file   );

if($popup==1){
  // Ouverture en Pop-up : si Image ou PDF : Preview
  if(strpos($file->file_type, "image") !== false || strpos($file->file_type, "pdf") !== false) {
    //Popup avec seconde previsualisation
    $listCat = new CFilesCategory;
    $listCat->load($file->file_category_id);
    
    $smarty->assign("listCat" , $listCat);
    $smarty->display("inc_preview_file_popup.tpl");
  }else{
    header("Location: mbfileviewer.php?file_id=$file_id");
  }
}else{
  $smarty->display("inc_preview_file.tpl");
}
?>
