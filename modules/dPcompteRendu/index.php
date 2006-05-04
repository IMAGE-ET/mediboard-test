<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

$canRead = !getDenyRead( $m );
$canEdit = !getDenyEdit( $m );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$AppUI->savePlace();

if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'dPcompteRenduIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPcompteRenduIdxTab' ) !== NULL ? $AppUI->getState( 'dPcompteRenduIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPcompteRenduIdxTab' ) );

$titleBlock = new CTitleBlock( 'Gestion des comptes-rendus', 'dPcompteRendu.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add('vw_modeles', 'liste des modèles');
$tabBox->add('addedit_modeles', 'Edition des modèles');
$tabBox->add('vw_idx_aides', 'Aides à la saisie');
$tabBox->add('vw_idx_listes', 'Listes de choix');
$tabBox->add('vw_idx_packs', 'Packs d\'hospitalisation');
$tabBox->show();

?>