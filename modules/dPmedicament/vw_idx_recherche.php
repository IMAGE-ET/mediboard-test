<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g, $dialog;

if(!CModule::getActive('bcb')){
	CAppUI::stepMessage(UI_MSG_ERROR, "Le module de médicament autonome est en cours de developpement. 
	Pour être utilisé, ce module a pour le moment besoin d'être connecté à une base de données de médicaments externe");
	return;
}

$search_by_cis = CValue::get("search_by_cis", 1);
$gestion_produits = CValue::get("gestion_produits", 0);
$_recherche_livret = CValue::get("_recherche_livret");

if($_recherche_livret){
  // Si selecteur de medicament en mode sejour, on recherche par default dans le livret
	$rechercheLivret = 1;
  $rechercheLivretComposant = 1;
  $rechercheLivretDCI = 1;
} else {
	$rechercheLivret = CValue::get("rechercheLivret", 0);
  $rechercheLivretComposant = CValue::get("rechercheLivretComposant", 0);
  $rechercheLivretDCI = CValue::get("rechercheLivretDCI", 0);
}

// Onglet ouvert par défaut
$modes_recherche = array("produit"   => "one",
                         "classe"    => "two",
                         "composant" => "three",
                         "DC_search" => "four");
                         
$onglet_recherche = $modes_recherche[CValue::get("onglet_recherche", "produit")];

$produit   = CValue::get("produit");
$composant = CValue::get("composant");

$composition = new CBcbComposition();
if($composant){
  $composition->searchComposant($composant);
}

$DCI = new CBcbDCI();
$DC_search = CValue::get("DC_search");
$tabDCI = array();

if($DC_search){
  $tabDCI = $DCI->searchDCI($DC_search);
}

// --- RECHERCHE PRODUITS ---
// Par default, recherche par nom
$type_recherche = CValue::get("type_recherche", "nom");

// Texte recherché (nom, cip, ucd)
$dialog = CValue::get("dialog");

// Recherche des elements supprimés
$supprime = CValue::get("supprime", 0);
$hors_specialite = CValue::get("hors_specialite", 0);

// Parametres de recherche
switch($type_recherche) {
	case "nom": $param_recherche = CValue::get("position_text", "debut"); break;
	case "cip": $param_recherche = '1'; break;
	case "ucd": $param_recherche = '2'; break;
}
$produits = array();

// Recherche de produits dans la BCB
$mbProduit = new CBcbProduit();

if($produit){
	if(!$rechercheLivret){
	  $produits = $mbProduit->searchProduit($produit, $supprime, $param_recherche, $hors_specialite, 200, null, null, $search_by_cis);
	}
	// Recherche de produits dans le livret Therapeutique
	if($rechercheLivret){
	  $produits = $mbProduit->searchProduit($produit, $supprime, $param_recherche, $hors_specialite, $max = 100, $livretTherapeutique = $g, null, $search_by_cis);
	}
}

// --- RECHERCHE PAR CLASSES ---
$classeATC = new CBcbClasseATC();
$classeATC->loadRefsProduits();
$arbreATC = $classeATC->loadArbre();
$niveauCodeATC = "";

$classeBCB = new CBcbClasseTherapeutique();
$classeBCB->loadRefsProduits();
$arbreBCB = $classeBCB->loadArbre();
$niveauCodeBCB = "";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("gestion_produits", $gestion_produits);
$smarty->assign("search_by_cis", $search_by_cis);
$smarty->assign("onglet_recherche", $onglet_recherche);
$smarty->assign("rechercheLivret" , $rechercheLivret );
$smarty->assign("rechercheLivretComposant", $rechercheLivretComposant);
$smarty->assign("rechercheLivretDCI", $rechercheLivretDCI);
$smarty->assign("composition"     , $composition     );
$smarty->assign("tabDCI"          , $tabDCI          );
$smarty->assign("DC_search"       , $DC_search       );
$smarty->assign("DCI_code"        , ""               );
$smarty->assign("tabViewProduit"  , ""               );
$smarty->assign("composant"       , $composant       );
$smarty->assign("code"            , ""               );
$smarty->assign("niveauCodeATC"   , $niveauCodeATC   );
$smarty->assign("niveauCodeBCB"   , $niveauCodeBCB   );
$smarty->assign("arbreATC"        , $arbreATC        );
$smarty->assign("arbreBCB"        , $arbreBCB        );
$smarty->assign("classeATC"       , $classeATC       );
$smarty->assign("classeBCB"       , $classeBCB       );
$smarty->assign("chapitreATC"     , ""               );
$smarty->assign("chapitreBCB"     , ""               );
$smarty->assign("codeATC"         , ""               );
$smarty->assign("codeBCB"         , ""               );
$smarty->assign("param_recherche" , $param_recherche );
$smarty->assign("dialog"          , $dialog          );
$smarty->assign("supprime"        , $supprime        );
$smarty->assign("type_recherche"  , $type_recherche  );
$smarty->assign("mbProduit"       , $mbProduit       );
$smarty->assign("produits"        , $produits        );
$smarty->assign("produit"         , $produit         );
$smarty->assign("hors_specialite", $hors_specialite);
$smarty->display("vw_idx_recherche.tpl");

?>