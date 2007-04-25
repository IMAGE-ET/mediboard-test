<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$examen_labo_id = mbGetValueFromGetOrSession("examen_labo_id");

// Chargement de l'examen demandé
$examen = new CExamenLabo;

// Chargement du catalogue demandé
if($examen_labo_id && !mbGetValueFromGet("catalogue_labo_id")) {
  $examen->load($examen_labo_id);
  $examen->loadRefs();
  $catalogue =& $examen->_ref_catalogue_labo;
} else {
  $catalogue_labo_id = mbGetValueFromGetOrSession("catalogue_labo_id");
  $catalogue = new CCatalogueLabo;
  $catalogue->load($catalogue_labo_id);
  $examen->catalogue_labo_id = $catalogue->_id;
}

$catalogue->loadRefs();

$groups = new CGroups;
$groups = $groups->loadList();
foreach ($groups as &$group) {
  $group->loadRefs();
}

//Chargement de tous les catalogues
$where = array("pere_id" => "IS NULL");
$order = "identifiant";
$listCatalogues = $catalogue->loadList($where, $order);
foreach($listCatalogues as $key => $curr_catalogue) {
  $listCatalogues[$key]->loadRefsDeep();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("examen"        , $examen   );
$smarty->assign("catalogue"     , $catalogue);
$smarty->assign("listCatalogues", $listCatalogues);

$smarty->display("vw_edit_examens.tpl");
?>
