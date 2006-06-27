<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

// [Begin] non-module specific code
 
$canRead = !getDenyRead($m);
$canEdit = !getDenyEdit($m);

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$AppUI->savePlace();

if (isset($_GET['tab'])) {
  $AppUI->setState("{$m}IdxTab", $_GET['tab']);
}

$tab = $AppUI->getState("{$m}IdxTab");
if (!$tab) {
  $tab = 0;
}

$active = intval(!$tab);

// [End] non-module specific code

$tabBox = new CTabBox("?m=$m", $AppUI->cfg['root_dir'] . "/modules/$m/", $tab );
$tabBox->add("import_orl", "Import ORL");
$tabBox->add("import_dermato", "Import Dermato");
$tabBox->add("export_hprim", "Export HPRIM");
$tabBox->add("send_mail", "Envoie de mails");
$tabBox->add("consult_anesth", "maj consult anesth");
$tabBox->add("codes_ccam", "maj codes ccam");
$tabBox->add("diag_patient", "maj diagnostics patients");
$tabBox->show();

?>