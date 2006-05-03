<?php /* $Id: vw_recherche.php,v 1.10 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.10 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPhospi", "affectation"));

// Récupération des paramètres
$typeVue = mbGetValueFromGetOrSession("typeVue");
$selPrat = mbGetValueFromGetOrSession("selPrat");

$date_recherche = mbGetValueFromGetOrSession("date_recherche", mbDateTime());

// Liste des chirurgiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

//
// Cas de l'affichage des lits libres
//
if($typeVue == 0) {
// Recherche de tous les lits disponibles
$sql = "SELECT lit.lit_id" .
		"\nFROM affectation" .
		"\nLEFT JOIN lit" .
		"\nON lit.lit_id = affectation.lit_id" .
		"\nWHERE '$date_recherche' BETWEEN affectation.entree AND affectation.sortie" .
		"\nGROUP BY lit.lit_id";
$occupes = db_loadlist($sql);
$arrayIn = array();
foreach($occupes as $key => $value) {
  $arrayIn[] = $occupes[$key]["lit_id"];
}
if(count($arrayIn)>0)
  $notIn = implode(", ", $arrayIn);
else
  $notIn = 0;

$sql = "SELECT lit.nom AS lit, chambre.nom AS chambre, service.nom AS service, MIN(affectation.entree) AS limite" .
		"\nFROM lit" .
		"\nLEFT JOIN affectation" .
		"\nON affectation.lit_id = lit.lit_id" .
		"\nAND (affectation.entree > '$date_recherche' OR affectation.entree IS NULL)" .
		"\nLEFT JOIN chambre" .
		"\nON chambre.chambre_id = lit.chambre_id" .
		"\nLEFT JOIN service" .
		"\nON service.service_id = chambre.service_id" .
		"\nWHERE lit.lit_id NOT IN($notIn)" .
		"\nGROUP BY lit.lit_id" .
		"\nORDER BY service.nom, limite DESC, chambre.nom, lit.nom";
$libre = db_loadlist($sql);
$listAff = null;
}

//
// Cas de l'affichage des lits d'un praticien
//
if ($typeVue == 1) {
  // Recherche des patients du praticien
  $date = mbDate(null, $date_recherche);

  $sql = "SELECT affectation.*" .
		"\nFROM affectation" .
		"\nLEFT JOIN lit" .
		"\nON affectation.lit_id = lit.lit_id" .
		"\nLEFT JOIN chambre" .
		"\nON chambre.chambre_id = lit.chambre_id" .
		"\nLEFT JOIN service" .
		"\nON service.service_id = chambre.service_id" .
		"\nLEFT JOIN operations" .
		"\nON operations.operation_id = affectation.operation_id" .
		"\nLEFT JOIN patients" .
		"\nON patients.patient_id = operations.pat_id" .
		"\nWHERE affectation.entree < '$date 23:59:59'" .
		"\nAND affectation.sortie > '$date 00:00:00'" .
		"\nAND operations.chir_id = '$selPrat'" .
		"\nORDER BY service.nom, chambre.nom, lit.nom";
  $listAff = new CAffectation;
  $listAff = db_loadObjectList($sql, $listAff);
  foreach($listAff as $key => $currAff) {
    $listAff[$key]->loadRefs();
    $listAff[$key]->_ref_operation->loadRefsFwd();
    $listAff[$key]->_ref_lit->loadRefsFwd();
    $listAff[$key]->_ref_lit->_ref_chambre->loadRefsFwd();
  }

  $libre = null;
}

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('date_recherche', $date_recherche);
$smarty->assign('libre', $libre);
$smarty->assign('typeVue', $typeVue);
$smarty->assign('selPrat', $selPrat);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('listAff', $listAff);

$smarty->display('vw_recherche.tpl');

?>