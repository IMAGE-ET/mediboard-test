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
$popup = mbGetValueFromGetOrSession("popup", 0);

$file = new CFile;
$file->load($file_id);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("file_id", $file_id);
$smarty->assign("file"   , $file   );

if($popup==1){
  $smarty->display("popup_view_file.tpl");
}else{
  $smarty->display("inc_preview_file.tpl");
}
?>
