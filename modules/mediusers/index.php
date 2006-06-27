<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
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
	$AppUI->setState( "mediusersIdxTab", $_GET['tab'] );
}
$tab = $AppUI->getState( "mediusersIdxTab" ) !== NULL ? $AppUI->getState( "mediusersIdxTab" ) : 0;

$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add( "vw_idx_mediusers", "Utilisateurs" );
$tabBox->add( "vw_idx_functions", "Fonctions des utilisateurs" );
$tabBox->add( "vw_idx_groups", "Groupes d'utilisateurs" );
$tabBox->show();

?>