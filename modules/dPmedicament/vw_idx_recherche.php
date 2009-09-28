<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g, $dPconfig, $dialog;

$search_by_cis = mbGetValueFromGet("search_by_cis", 1);
$gestion_produits = mbGetValueFromGet("gestion_produits", 0);
$_recherche_livret = mbGetValueFromGet("_recherche_livret");

if($_recherche_livret){
  // Si selecteur de medicament en mode sejour, on recherche par default dans le livret
	$rechercheLivret = 1;
  $rechercheLivretComposant = 1;
  $rechercheLivretDCI = 1;
} else {
	$rechercheLivret = mbGetValueFromGet("rechercheLivret", 0);
  $rechercheLivretComposant = mbGetValueFromGet("rechercheLivretComposant", 0);
  $rechercheLivretDCI = mbGetValueFromGet("rechercheLivretDCI", 0);
}

// Onglet ouvert par défaut
$modes_recherche = array("produit"   => "one",
                         "classe"    => "two",
                         "composant" => "three",
                         "DC_search" => "four");
                         
$onglet_recherche = $modes_recherche[mbGetValueFromGet("onglet_recherche", "produit")];

$produit   = mbGetValueFromGet("produit");
$composant = mbGetValueFromGet("composant");

$composition = new CBcbComposition();
if($composant){
  $composition->searchComposant($composant);
}

$DCI = new CBcbDCI();
$DC_search = mbGetValueFromGet("DC_search");
$tabDCI = array();

if($DC_search){
  $tabDCI = $DCI->searchDCI($DC_search);
}

// --- RECHERCHE PRODUITS ---
// Par default, recherche par nom
$type_recherche = mbGetValueFromGet("type_recherche", "nom");

// Texte recherché (nom, cip, ucd)
$dialog = mbGetValueFromGet("dialog");

// Recherche des elements supprimés
$supprime = mbGetValueFromGet("supprime", 0);
$hors_specialite = mbGetValueFromGet("hors_specialite", 0);

// Parametres de recherche
switch($type_recherche) {
	case "nom": $param_recherche = mbGetValueFromGet("position_text", "debut"); break;
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