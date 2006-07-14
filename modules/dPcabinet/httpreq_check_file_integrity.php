<?php /* $Id: httpreq_vw_consult_anesth.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass("dPcabinet", "files"));

$limit = mbGetValueFromGet("limit", 50);
  
set_time_limit(90);

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$docs = new CFile();
$docs = $docs->loadList();
$docsWithoutFile = array();
foreach($docs as $keyDoc => $valDoc) {
  $doc =& $docs[$keyDoc];
  if (!is_file($doc->_file_path)) {
    $docsWithoutFile[$keyDoc] =& $doc;
  }
}

$files = glob("files/*/*/*/*");
$filesWithoutDoc = array();
foreach($files as $filePath) {
  $fileName = basename($filePath);
  $fileObjectId = basename(dirname($filePath));
  $fileObjectClass = basename(dirname(dirname(dirname($filePath))));

  $where = array(
    "file_real_filename" => "= '$fileName'",
    "file_object_id" => "= '$fileObjectId'",
    "file_class" => "= '$fileObjectClass'",
  );

  $doc = new CFile;
  $doc->loadObject($where);
  if (!$doc->file_id) {
    $filesWithoutDoc[] = array(
      "fileName" => $fileName,
      "fileObjectId" => $fileObjectId,
      "fileObjectClass" => $fileObjectClass,
      "filePath" => $filePath,
    );
  }
}

$docsWithoutFileTruncated = array_slice($docsWithoutFile, 0, $limit);
$filesWithoutDocTruncated = array_slice($filesWithoutDoc, 0, $limit);

// Création du template
require_once( $AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("limit", $limit);
$smarty->assign("docs", $docs);
$smarty->assign("docsWithoutFile", $docsWithoutFile);
$smarty->assign("docsWithoutFileTruncated", $docsWithoutFileTruncated);
$smarty->assign("files", $files);
$smarty->assign("filesWithoutDoc", $filesWithoutDoc);

$smarty->display("inc_check_file_integrity.tpl");

?>