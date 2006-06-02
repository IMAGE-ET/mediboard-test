<?php /* SYSTEM $Id$ */
$AppUI->savePlace();

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

$titleBlock = new CTitleBlock( 'System Administration', '48_my_computer.png', $m, "$m.$a" );
$titleBlock->show();

$tabBox = new CTabBox("?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab);
$tabBox->add("view_dpadmin", "Configuration générale");
$tabBox->add("view_history", "Historique");
$tabBox->add("view_messages", "Messagerie");
$tabBox->add("view_logs", "Logs système");
$tabBox->add("view_access_logs", "Logs d'accès");
$tabBox->show();
?>
