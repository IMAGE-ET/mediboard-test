<?php /* $Id: index.php,v 1.5 2006/04/27 16:28:03 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.5 $
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

$titleBlock = new CTitleBlock( 'Reporting', 'dPstats.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=dPstats", "{$AppUI->cfg['root_dir']}/modules/dPstats/", $tab );
$tabBox->add( 'vw_activite', 'Activite' );
$tabBox->add( 'vw_hospitalisation', 'Hospitalisation');
$tabBox->add( 'vw_bloc', 'Bloc opératoire');
$tabBox->add( 'vw_time_op', 'Temps opératoires');
$tabBox->add( 'vw_users', 'Utilisateurs');
$tabBox->show();

?>