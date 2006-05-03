<?php /* $Id: vw_idx_salles.php,v 1.8 2006/04/21 16:56:38 mytto Exp $ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision: 1.8 $
 *  @author Romain Ollivier
 */
 
global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass($m, 'salle'));

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Récupération des salles
$order[] = "nom";
$salles = new CSalle;
$salles = $salles->loadList(null, $order); 

// Récupération de la salle à ajouter/editer
$salleSel = new CSalle;
$salleSel->load(mbGetValueFromGetOrSession('salle_id'));

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('salles', $salles);
$smarty->assign('salleSel', $salleSel);

$smarty->display('vw_idx_salles.tpl');

?>