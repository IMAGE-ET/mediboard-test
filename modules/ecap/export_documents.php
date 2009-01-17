<?php

/**
* @package Mediboard
* @subpackage ecap
* @version $Revision: 2165 $
* @author Thomas Despoix
*/

global $can;
$can->needsRead();

$do = mbGetValueFromGet("do");
$max = mbGetValueFromGetOrSession("max", 20);

$classes = array("CPatient", "CSejour", "CIntervention");
$file = new CFile();
$ds =  $file->_spec->ds;
$where["object_class"] = $ds->prepareIn($classes);
$order = "file_date";
$limit = "0, $max";
$files = $file->loadList($where, $order, $limit);
$files_count = $file->countList($where);

CMedicap::makeTags();
foreach($files as $_file) {
  $_file->loadTargetObject();
  $idExt = new CIdSante400;
	$idExt->loadLatestFor($_file, CMedicap::$tags["DOC"]);
  $_file->_ref_id_ecap = $idExt;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("do", $do);
$smarty->assign("max", $max);
$smarty->assign("classes", $classes);
$smarty->assign("files", $files);
$smarty->assign("files_count", $files_count);

$smarty->display("export_documents.tpl");
?>