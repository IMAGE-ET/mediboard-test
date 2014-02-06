<?php

/* $Id$ */

/**
 * @package Mediboard
 * @subpackage oscour
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license OXPL
 */

global $m;

CCanDo::checkAdmin();
$module = CValue::get("module");
$file = isset($_FILES['import']) ? $_FILES['import'] : null;

$fingerprint = $keydata = null;
if ($file) {
  $keydata = file_get_contents($file['tmp_name']);
  if ($module) {
    $path = CAppUI::conf("$module gnupg_path");
  }
  $gpg = new gnupg();
  if ($module && $path) {
    putenv("HOME=$path");
  }
  $gpg->seterrormode(gnupg::ERROR_EXCEPTION);
  try{
    $info = $gpg->import($keydata);
  }
  catch(Exception $e) {
    mbTrace($e->getMessage());
  }

  if (array_key_exists("fingerprint", $info)) {
    $fingerprint = $info['fingerprint'];
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("fingerprint", $fingerprint);
$smarty->assign("keydata"    , $keydata);

$smarty->display("ajax_import_key.tpl");