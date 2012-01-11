<?php

CCanDo::checkEdit();

$file = $_FILES["fileupload"];

$token = str_replace(".", "", uniqid("imp_", true));
$new_path = CAppUI::getTmpPath("object_import/$token");
CMbPath::forceDir(dirname($new_path));

move_uploaded_file($file["tmp_name"], $new_path);

$dom = new CMbXMLObjectExport();
$dom->load($new_path);

$smarty = new CSmartyDP();
$smarty->assign("token", $token);
$smarty->assign("dom", $dom);
$smarty->display("inc_object_import_configure.tpl");
