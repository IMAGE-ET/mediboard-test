<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$canRead = !getDenyRead( $m );
$canEdit = !getDenyEdit( $m );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

//save the workplace state
$AppUI->savePlace();

// retrieve any state parameters (temporary session variables that are not stored in db)

if (isset( $_GET['tab'] )) {
	// saves the current tab box state
	$AppUI->setState( "dPcim10IdxTab", $_GET['tab'] );
}
$tab = $AppUI->getState( "dPcim10IdxTab" ) !== NULL ? $AppUI->getState( "dPcim10IdxTab" ) : 1;

// build new tab box object
$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add( "vw_idx_favoris", "Mes favoris" );
$tabBox->add( "vw_find_code"  , "Rechercher un code" );
$tabBox->add( "vw_full_code"  , "Affichage d'un code" );
$tabBox->add( "vw_idx_chapter"  , "Sommaire de la CIM10" );
$tabBox->show();

?>