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

//save the workplace state (have a footprint on this site)
$AppUI->savePlace();

// retrieve any state parameters (temporary session variables that are not stored in db)

if (isset( $_GET['tab'] )) {
	// saves the current tab box state
	$AppUI->setState( "mediusersIdxTab", $_GET['tab'] );
}
$tab = $AppUI->getState( "mediusersIdxTab" ) !== NULL ? $AppUI->getState( "mediusersIdxTab" ) : 0;

// we prepare the User Interface Design with the dPFramework

// setup the title block with Name, Icon and Help
$titleBlock = new CTitleBlock( "Gestion des utilisateurs", "mediusers.png", $m, "$m.$a" );
$titleBlock->show();

// now prepare and show the tabbed information boxes with the dPFramework

// build new tab box object
$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add( "vw_idx_mediusers", "Utilisateurs" );
$tabBox->add( "vw_idx_functions", "Fonctions des utilisateurs" );
$tabBox->add( "vw_idx_groups", "Groupes d'utilisateurs" );
$tabBox->show();

?>