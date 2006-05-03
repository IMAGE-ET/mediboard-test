<?php /* $Id: index.php,v 1.8 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision: 1.8 $
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

$titleBlock = new CTitleBlock( 'Gestion des admissions', "$m.png", $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add( 'vw_idx_admission', 'Consultation des admissions' );
$tabBox->add( 'vw_idx_sortie', 'Validation des sorties' );
$tabBox->show();

?>