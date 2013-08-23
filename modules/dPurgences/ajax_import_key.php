<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage oscour
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license OXPL
 */

CCanDo::checkAdmin();

$file = isset($_FILES['import']) ? $_FILES['import'] : null;

$fingerprint = $keydata = null;
if ($file) {
  $keydata = file_get_contents($file['tmp_name']);

  $gpg = new gnupg();
  $info = $gpg->import($keydata);

  if (array_key_exists("fingerprint", $info)) {
    $fingerprint = $info['fingerprint'];
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("fingerprint", $fingerprint);
$smarty->assign("keydata"    , $keydata);

$smarty->display("ajax_import_key.tpl");