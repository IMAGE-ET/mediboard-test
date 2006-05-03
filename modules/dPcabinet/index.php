<?php /* $Id: index.php,v 1.7 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
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
	$AppUI->setState( 'dPcabinetIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPcabinetIdxTab' ) !== NULL ? $AppUI->getState( 'dPcabinetIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPcabinetIdxTab' ) );

$titleBlock = new CTitleBlock( 'Gestion de cabinet de consultation', 'dPcabinet.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=dPcabinet", "{$AppUI->cfg['root_dir']}/modules/dPcabinet/", $tab );
$tabBox->add( 'vw_planning', 'Programmes de consultation' );
$tabBox->add( 'edit_planning', 'Créer / Modifier un rendez-vous' );
$tabBox->add( 'edit_consultation', 'Consultation' );
$tabBox->add( 'vw_dossier', 'Dossiers' );
//$tabBox->add( 'idx_compte_rendus', 'Compte-rendus');
$tabBox->add( 'form_print_plages', 'Impression des plannings' );
$tabBox->add( 'vw_compta', 'Comptabilité' );
$tabBox->show();

?>