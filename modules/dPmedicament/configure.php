<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author Alexis Granger
 */

global $can;
$can->needsAdmin();

// Chargement des catégories
$category = new CProductCategory();
$categories_list = $category->loadList(null, 'name');

// Analyse des data source BCB
$states = array("bcb1" => null, "bcb2" => null);
foreach ($states as $dsn => &$state) {
  if (null == $ds =  @CSQLDataSource::get($dsn)) {
    continue;
  }
  
  $state["rows_count"] = 0;
  foreach ($ds->loadList("SHOW TABLE STATUS") as $table) {
    $state["rows_count"] += $table["Rows"];
  }
  
  $state["last_modif"] = $ds->loadTable('HISTORIQUES') ? 
    $ds->loadResult("SELECT MAX( `DATE_`) FROM `HISTORIQUES`") :
    "Unavailable";
    
  $state["version"] = $ds->loadTable('HISTORIQUE_PRODUITS_MODIFIES') ? 
    $ds->loadResult("SELECT MAX( `DATE_`) FROM `HISTORIQUE_PRODUITS_MODIFIES`") :
    "Unavailable";
}

// Analyse des livrets thérapeutiques
if (null == $dsBCBmed = @CBcbObject::getDataSource()) {
  CAppUI::stepMessage(UI_MSG_WARNING, "DataSource-bcb_med-ko");
}
else {
	if (!$dsBCBmed->loadTable('livrettherapeutique')) {
	  $dsBCBmed = null;
	  CAppUI::stepMessage(UI_MSG_WARNING, "DataSource-bcb_med-empty");
	}
}

if (null == $dsBCBges = @CSQLDataSource::get("bcbges")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "DataSource-bcb-ges-ko");
}
else {
	if (!$dsBCBges->loadTable('livrettherapeutique')) {
	  $dsBCBges = null;
	  CAppUI::stepMessage(UI_MSG_WARNING, "DataSource-bcb_ges-empty");
	}
}

$nb_produit_livret_med = $dsBCBmed ? CBcbProduitLivretTherapeutique::countProduitsMed() : null;
$nb_produit_livret_ges = $dsBCBges ? CBcbProduitLivretTherapeutique::countProduitsGes() : null;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('states', $states);
$smarty->assign('categories_list', $categories_list);
$smarty->assign('nb_produit_livret_med', $nb_produit_livret_med);
$smarty->assign('nb_produit_livret_ges', $nb_produit_livret_ges);

$smarty->display("configure.tpl");

?>