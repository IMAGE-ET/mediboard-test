<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
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

$titleBlock = new CTitleBlock("Planning de l'hospitalisation", "$m.png", $m, "$m.$a");
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox("?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );

$tabBox->add("form_print_planning", "Impression des plannings");
$tabBox->add("edit_sorties", "Déplacements / Sorties");
$tabBox->add("vw_recherche", "Chercher une chambre");
if ($canEdit) {
  $tabBox->add("vw_affectations", "Affectations");
  $tabBox->add("vw_idx_services", "Services");
  $tabBox->add("vw_idx_chambres", "Chambres");
}

$tabBox->show();
?>