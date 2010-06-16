<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

global $can, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

// Récupération des paramètres
$typeVue = CValue::getOrSession("typeVue");
$selPrat = CValue::getOrSession("selPrat");
$selService = CValue::getOrSession("selService");

$date_recherche = CValue::getOrSession("date_recherche", mbDateTime());

// Liste des chirurgiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Liste des services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

// Where permettant de trier suivant les services
$whereService = "AND service.service_id  " .CSQLDataSource::prepareIn(array_keys($services));
if($selService){
  $whereService = "AND service.service_id  = '$selService'";
}

$listAff = null;
$libre = null;

//
// Cas de l'affichage des lits libres
//
if($typeVue == 0) {
	// Recherche de tous les lits disponibles
	$sql = "SELECT lit.lit_id
					FROM affectation
				  LEFT JOIN lit ON lit.lit_id = affectation.lit_id
				  LEFT JOIN chambre ON lit.chambre_id = chambre.chambre_id
					WHERE '$date_recherche' BETWEEN affectation.entree AND affectation.sortie
					AND chambre.annule = '0'
					AND affectation.effectue = '0'
				  GROUP BY lit.lit_id";
	$occupes = $ds->loadlist($sql);
	$arrayIn = array();
	foreach($occupes as $key => $occupe) {
	  $arrayIn[] = $occupe["lit_id"];
	}
	$notIn = count($arrayIn) > 0 ? implode(', ', $arrayIn) : 0;
	
	$sql = "SELECT lit.nom AS lit, chambre.nom AS chambre, service.nom AS service, MIN(affectation.entree) AS limite
					FROM lit
					LEFT JOIN affectation ON affectation.lit_id = lit.lit_id
					AND (affectation.entree > '$date_recherche' OR affectation.entree IS NULL)
					LEFT JOIN chambre ON chambre.chambre_id = lit.chambre_id
					LEFT JOIN service ON service.service_id = chambre.service_id
					WHERE lit.lit_id NOT IN($notIn)
					AND chambre.annule = '0'
			    AND service.group_id = '$g'
			    $whereService
					GROUP BY lit.lit_id
					ORDER BY service.nom, chambre.nom, lit.nom, limite DESC";
	$libre = $ds->loadlist($sql);
}

//
// Cas de l'affichage des lits d'un praticien
//
else if ($typeVue == 1) {
  // Recherche des patients du praticien
  $date_recherche;
  
  if($selPrat) {
    $wherePrat = "AND sejour.praticien_id = '$selPrat'";
  } else {
    $wherePrat = "AND sejour.praticien_id  " .CSQLDataSource::prepareIn(array_keys($listPrat));
  }

  $sql = "SELECT affectation.*
					FROM affectation
					LEFT JOIN lit ON affectation.lit_id = lit.lit_id
					LEFT JOIN chambre ON chambre.chambre_id = lit.chambre_id
					LEFT JOIN service ON service.service_id = chambre.service_id
					LEFT JOIN sejour ON sejour.sejour_id = affectation.sejour_id
					WHERE affectation.entree < '$date_recherche'
					AND affectation.sortie > '$date_recherche'
					$whereService
			    $wherePrat
			    AND sejour.group_id = '$g'
					ORDER BY service.nom, chambre.nom, lit.nom";
  $listAff = new CAffectation;
  $listAff = $listAff->loadQueryList($sql);
  foreach($listAff as &$aff) {
    $aff->loadRefSejour();
    $aff->_ref_sejour->loadRefPatient();
    $aff->_ref_sejour->_ref_praticien =& $listPrat[$aff->_ref_sejour->praticien_id];
    $aff->_ref_sejour->loadRefGHM();

    $aff->loadRefLit();
    $aff->_ref_lit->loadCompleteView();
		foreach($aff->_ref_sejour->_ref_operations as $_operation){
			$_operation->loadExtCodesCCAM();
		}
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_recherche", $date_recherche);
$smarty->assign("libre"         , $libre);
$smarty->assign("typeVue"       , $typeVue);
$smarty->assign("selPrat"       , $selPrat);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("listAff"       , $listAff);
$smarty->assign("selService"    , $selService);
$smarty->assign("services"      , $services);
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));

$smarty->display("vw_recherche.tpl");

?>