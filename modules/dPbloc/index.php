<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
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
	$AppUI->setState( 'dPblocIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPblocIdxTab' ) !== NULL ? $AppUI->getState( 'dPblocIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPblocIdxTab' ) );

$tabBox = new CTabBox( "?m=dPbloc", "{$AppUI->cfg['root_dir']}/modules/dPbloc/", $tab );
if($canEdit) {
  $tabBox->add( "vw_idx_planning", "Planning de la semaine" );
  $tabBox->add( "vw_edit_plages", "Planning du jour" );
  $tabBox->add( "vw_edit_interventions", "Gestion des interventions" );
  $tabBox->add( "vw_urgences", "Voir les urgences");
  $tabBox->add( "vw_idx_materiel", "Commande de matériel" );
  $tabBox->add( "vw_idx_salles", "Gestion des salles" );
}
$tabBox->add( "print_planning", "Impression des plannings" );

$tabBox->show();
?>