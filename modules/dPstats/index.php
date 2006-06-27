<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
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
	$AppUI->setState( 'dPstatsIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPstatsIdxTab' ) !== NULL ? $AppUI->getState( 'dPstatsIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPstatsIdxTab' ) );

$tabBox = new CTabBox( "?m=dPstats", "{$AppUI->cfg['root_dir']}/modules/dPstats/", $tab );
$tabBox->add( 'vw_activite', 'Activite' );
$tabBox->add( 'vw_hospitalisation', 'Hospitalisation');
$tabBox->add( 'vw_bloc', 'Bloc opératoire');
$tabBox->add( 'vw_time_op', 'Temps opératoires');
$tabBox->add( 'vw_users', 'Utilisateurs');
$tabBox->show();

?>