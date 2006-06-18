<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
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

$titleBlock = new CTitleBlock("Planning des chirurgiens", "$m.png", $m, "$m.$a");
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox("?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );

if ($canEdit) {
  $tabBox->add("vw_idx_planning", "Consulter le planning");
  $tabBox->add("vw_edit_planning", "Planifier / Modifier une intervention");
}

//$tabBox->add("vw_edit_hospi", "Planifier / Modifier une hospitalisation");
$tabBox->add("vw_edit_sejour", "Planifier / Modifier un séjour");

if ($canEdit) {
  $tabBox->add("vw_protocoles", "Protocoles");
  $tabBox->add("vw_edit_protocole", "Créer / Modifier un protocole");
}

$tabBox->show();
?>