<?php /* $Id: httpreq_vw_consult_anesth.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass("dPcabinet", "files"));

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

set_time_limit(90);

$show = mbGetValueFromGet("show", 50);

// Search document without files
$stepSize = 1000;
$step = 0;

$docsWithoutFileTruncated = array();
$docsCount = 0;
$docsWithoutFileCount = 0;
do {
  $offset = $step * $stepSize;  
  
  $limit = "$offset, $stepSize";
  $docs = new CFile();
  $docs = $docs->loadList(null, null, $limit);
  foreach($docs as $keyDoc => $valDoc) {
    $doc =& $docs[$keyDoc];
    $docsCount++;
    if (!is_file($doc->_file_path)) {
      $docsWithoutFileCount++;
      if (count($docsWithoutFileTruncated) < $show) {
        $docsWithoutFileTruncated[$keyDoc] =& $doc;
      }
    }
  }
  $step++;
} while (count($docs));

// Search files without documents
$files = glob("files/*/*/*/*");
$filesCount = 0;
$filesWithoutDocCount = 0;
$filesWithoutDocTruncated = array();
foreach($files as $filePath) {
  $filesCount++;
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
    $filesWithoutDocCount++;
    if (count($filesWithoutDocCount) < $show) {
      $filesWithoutDocTruncated[] = array(
        "fileName" => $fileName,
        "fileObjectId" => $fileObjectId,
        "fileObjectClass" => $fileObjectClass,
        "filePath" => $filePath,
      );
    }
  }
}


// Création du template
require_once( $AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("show", $show);
$smarty->assign("docsWithoutFileTruncated", $docsWithoutFileTruncated);
$smarty->assign("docsWithoutFileCount", $docsWithoutFileCount);
$smarty->assign("docsCount", $docsCount);
$smarty->assign("filesWithoutDocTruncated", $filesWithoutDocTruncated);
$smarty->assign("filesWithoutDocCount", $filesWithoutDocCount);
$smarty->assign("filesCount", $filesCount);

$smarty->display("inc_check_file_integrity.tpl");

?>