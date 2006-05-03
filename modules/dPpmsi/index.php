<?php /* $Id: index.php,v 1.7 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: 1.7 $
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
$tabBox->add( 'labo_groupage', 'Labo groupage GHM' );
$tabBox->show();

?>