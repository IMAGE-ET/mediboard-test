<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m, $g;
$ds = CSQLDataSource::get("std");

$can->needsRead();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// R�cup�ration des salles
$order = array();
$order[] = "group_id, nom";
$where = array();
$where["group_id"] = $ds->prepareIn(array_keys($etablissements));
$salle = new CSalle;
$salles = $salle->loadListWithPerms(PERM_EDIT, $where, $order);
foreach($salles as $keySalle=>$valSalle){
  $salles[$keySalle]->loadRefsFwd();
} 

// R�cup�ration de la salle � ajouter/editer
$salleSel = new CSalle;
$salleSel->load(mbGetValueFromGetOrSession("salle_id"));

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("salles"          , $salles        );
$smarty->assign("salleSel"        , $salleSel      );
$smarty->assign("etablissements"  , $etablissements);

$smarty->display("vw_idx_salles.tpl");

?>