<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $canRead, $canEdit, $m, $g;

require_once($AppUI->getModuleClass($m, "salle"));
require_once($AppUI->getModuleClass("dPetablissement"  , "groups" ));


if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération des salles
$order[] = "group_id, nom";
$where["group_id"] = "IN (".implode(array_keys($etablissements),", ").")";
$salles = new CSalle;
$salles = $salles->loadList($where, $order);
foreach($salles as $keySalle=>$valSalle){
  $salles[$keySalle]->loadRefsFwd();
} 

// Récupération de la salle à ajouter/editer
$salleSel = new CSalle;
$salleSel->load(mbGetValueFromGetOrSession("salle_id"));

// Création du template
require_once( $AppUI->getSystemClass ("smartydp") );
$smarty = new CSmartyDP(1);

$smarty->assign("salles"          , $salles        );
$smarty->assign("salleSel"        , $salleSel      );
$smarty->assign("etablissements"  , $etablissements);

$smarty->display("vw_idx_salles.tpl");

?>