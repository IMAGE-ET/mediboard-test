<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
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
	$AppUI->setState( 'dPpmsiIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPpmsiIdxTab' ) !== NULL ? $AppUI->getState( 'dPpmsiIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPpmsiIdxTab' ) );

$titleBlock = new CTitleBlock( 'Gestion des actes PMSI', 'dPpmsi.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=dPpmsi", "{$AppUI->cfg['root_dir']}/modules/dPpmsi/", $tab );
$tabBox->add( 'vw_dossier', 'Dossiers patient' );
$tabBox->add( 'edit_actes', 'Codage des actes' );
$tabBox->add( 'labo_groupage', 'Groupage GHM' );
$tabBox->add( 'vw_list_hospi', 'Liste des hospitalisations' );
$tabBox->show();

?>