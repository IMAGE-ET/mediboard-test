<?php /* $Id: httpreq_vw_consult_anesth.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass("dPcabinet", "files"));
  
set_time_limit(90);

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$where["file_id"] = "< '1000'";
$file = new CFile();
$files = $file->loadList($where);

// Création du template
require_once( $AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("files", $files);

$smarty->display("inc_check_file_integrity.tpl");

?>