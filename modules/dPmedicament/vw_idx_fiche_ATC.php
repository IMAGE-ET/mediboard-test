<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if(!CModule::getActive('bcb')){
  CAppUI::stepMessage(UI_MSG_ERROR, "Le module de médicament autonome est en cours de developpement. 
  Pour être utilisé, ce module a pour le moment besoin d'être connecté à une base de données de médicaments externe");
  return;
}

$fiche_ATC_id = CValue::getOrSession("fiche_ATC_id");

// Chargement de la fiche selectionnee
$fiche_ATC = new CFicheATC();
if($fiche_ATC_id){
  $fiche_ATC->load($fiche_ATC_id);
  $fiche_ATC->getLibelleATC();
	$templateManager = new CTemplateManager();
	$templateManager->editor = "fckeditor";
	$templateManager->simplifyMode = true;
	$templateManager->initHTMLArea();
}

// Initialisation du tableau de conversion de Code ATC à libelle ATC
$code_to_libelle = array();
$fiches = array();

// Chargement de l'arbre ATC de niveau 1 et 2
$classes_ATC = array();
$classe_ATC = new CBcbClasseATC();
$classe_ATC->loadArbre();
foreach($classe_ATC->distObj->tabClasseATC as $classe_ATC_1){
  $libelle_ATC_1 = strtolower($classe_ATC_1->Libelle);
  @$code_to_libelle[$classe_ATC_1->Code] = $libelle_ATC_1;
  $classe_ATC->loadArbre($classe_ATC_1->Code);
  foreach($classe_ATC->distObj->tabClasseATC as $classe_ATC_2){
    $libelle_ATC_2 = strtolower($classe_ATC_2->Libelle);
    @$code_to_libelle[$classe_ATC_2->Code] = $libelle_ATC_2;
    $classes_ATC[$libelle_ATC_1][$classe_ATC_2->Code] = $libelle_ATC_2;
  }
}

// Chargement de toutes les fiches ATC
$list_fiches = $fiche_ATC->loadList();
foreach($list_fiches as $_fiche){
  $fiches[$_fiche->code_ATC[0]][$_fiche->code_ATC][$_fiche->_id] = $_fiche;
  ksort($fiches[$_fiche->code_ATC[0]]);
}
ksort($fiches);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("fiche_ATC", $fiche_ATC);
$smarty->assign("fiches", $fiches);
$smarty->assign("classes_ATC", $classes_ATC);
$smarty->assign("code_to_libelle", $code_to_libelle);
$smarty->display("vw_idx_fiche_ATC.tpl");


?>