<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPfiles", "files"));

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
  header("Location: mbfileviewer.php?file_id=$file_id");
}else{
  $smarty->display("inc_preview_file.tpl");
}
?>
