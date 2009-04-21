<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$commande_id = mbGetValueFromGetOrSession("commande_materiel_id");

// Chargement de la commande demandé
$commande = new CCommandeMateriel();
$commande->load($commande_id);

$order = "date DESC";

//Chargement des commandes recevoir
$where = array("recu" => "= 1");
$listCommandesARecevoir = $commande->loadList($where,$order);
foreach($listCommandesARecevoir as &$curr_comm) {
  $curr_comm->loadRefsFwd();
}

//Chargement des commandes recu
$where = array("recu" => "<> 1");
$listCommandesRecu = $commande->loadList($where,$order);

foreach($listCommandesRecu as &$curr_comm) {
  $curr_comm->loadRefsFwd();
}

//Liste des références
$reference = new CRefMateriel();
$listReferences = $reference->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("commande"      , $commande     );
$smarty->assign("listCommandesARecevoir" , $listCommandesARecevoir);
$smarty->assign("listCommandesRecu" , $listCommandesRecu);
$smarty->assign("listReferences", $listReferences);

$smarty->display("vw_idx_commandes.tpl");
?>
