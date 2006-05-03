<?php /* $Id: index.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: 1.2 $
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

$titleBlock = new CTitleBlock( 'Gestion comptable de cabinet', 'dPgestionCab.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=dPgestionCab", "{$AppUI->cfg['root_dir']}/modules/dPgestionCab/", $tab );
$tabBox->add( 'edit_compta', 'Comptabilité' );
$tabBox->add( 'edit_paie', 'Paie' );
$tabBox->show();

?>