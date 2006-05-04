<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
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
	$AppUI->setState( 'dPsalleOpIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPsalleOpIdxTab' ) !== NULL ? $AppUI->getState( 'dPsalleOpIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPsalleOpIdxTab' ) );

$titleBlock = new CTitleBlock( 'Gestion des salles d\'operation', 'dPsalleOp.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=dPsalleOp", "{$AppUI->cfg['root_dir']}/modules/dPsalleOp/", $tab );
$tabBox->add( 'vw_operations', 'Salles d\'opération' );
$tabBox->add( 'vw_reveil', 'Salle de reveil' );
$tabBox->show();

?>