<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
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
	$AppUI->setState( "dPccamIdxTab", $_GET['tab'] );
}
$tab = $AppUI->getState( "dPccamIdxTab" ) !== NULL ? $AppUI->getState( "dPccamIdxTab" ) : 1;

$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add( "vw_idx_favoris", "Mes favoris" );
$tabBox->add( "vw_find_code"  , "Rechercher un code" );
$tabBox->add( "vw_full_code"  , "Affichage d'un code" );
$tabBox->show();

?>