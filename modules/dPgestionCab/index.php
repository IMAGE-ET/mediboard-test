<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
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

$tabBox = new CTabBox( "?m=dPgestionCab", "{$AppUI->cfg['root_dir']}/modules/dPgestionCab/", $tab );
$tabBox->add( 'edit_compta', 'Comptabilité' );
$tabBox->add( 'edit_paie', 'Fiche de paie' );
$tabBox->add( 'edit_params', 'Paramètres' );
$tabBox->show();

?>