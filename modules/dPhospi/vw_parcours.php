<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Poiron Yohann
*/

global $AppUI, $can, $m, $g;
$can->needsRead();

$today = mbDateTime();
$sejour_id 		= CValue::getOrSession("sejour_id",0);
$operation_id = CValue::getOrSession("operation_id",0);

function cmp_dateDesc($arrA, $arrB)
{
    if ($arrA->_datetime == $arrB->_datetime) return 0;
			return $arrA->_datetime < $arrB->_datetime ? -1 : 1;
}

function cmp_dateAsc($arrA, $arrB)
{
    if ($arrA->_datetime == $arrB->_datetime) return 0;
			return $arrA->_datetime > $arrB->_datetime ? -1 : 1;
}

// Récuperation du sejour sélectionné
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefs();
$sejour->loadRefsAffectations();   
$operations = $sejour->_ref_operations;
$affectations = $sejour->_ref_affectations;
$datesOperation = array();

foreach ($operations as $key) {
	$key->loadRefPlageOp();
	$datesOperation[$key->operation_id]['id'] = $key->operation_id;
	$datesOperation[$key->operation_id]['date'] = $key->_datetime;
	$datesOperation[$key->operation_id]['entree_salle'] = $key->entree_salle;
	$datesOperation[$key->operation_id]['sortie_salle'] = $key->sortie_salle;
	$datesOperation[$key->operation_id]['entree_reveil'] = $key->entree_reveil;
	$datesOperation[$key->operation_id]['sortie_reveil'] = $key->sortie_reveil;
}

$tabOperationCurrent = array();
$tabOperationExpect = array();
$tabOperationDone = array();

$diagramme = array();

foreach ($datesOperation as &$date) {
	$estEnSalle = $date['entree_salle'] && $date['sortie_salle'] == null;
	$estSortieSalle = $date['sortie_salle'] && $date['entree_reveil'] == null;
	$estSalleReveil = $date['sortie_salle'] && $date['entree_reveil'];
	$estSortieSalleReveil = $date['entree_salle'] && $date['sortie_salle'] && $date['entree_reveil'] && $date['sortie_reveil'];
	
	if ($date['entree_salle'] == null) {
		$tabOperationExpect[] = $operations[$date['id']];
	}
	if ($estEnSalle || $estSortieSalle || $estSalleReveil) {
		$tabOperationCurrent = $operations[$date['id']];
	}
	if ($estSortieSalleReveil) {
		$tabOperationDone[] = $operations[$date['id']];
	}
}

uasort($tabOperationExpect, 'cmp_dateDesc');
uasort($tabOperationDone, 'cmp_dateAsc');

$operation = null;
if ($operation_id != null) {
	$operations = array();
	// Récuperation du sejour sélectionné
	$operation = new COperation;
	$operation->load($operation_id);
	$operation->loadRefsFwd();
	$estEnSalle = $operation->entree_salle && $operation->sortie_salle == null;
	$estSortieSalle = $operation->sortie_salle && $operation->entree_reveil == null;
	$estSalleReveil = $operation->sortie_salle && $operation->entree_reveil;
	$estSortieSalleReveil = $operation->entree_salle && $operation->sortie_salle && $operation->entree_reveil && $operation->sortie_reveil;
	if ($operation->entree_salle == null) {
		$diagramme['bloc']['type'] = "expect";
	}
	if ($estEnSalle || $estSortieSalle || $estSalleReveil) {
		$diagramme['bloc']['type'] = "current";
	}
	if ($estSortieSalleReveil) {
		$diagramme['bloc']['type'] = "done";
	}
	if (count($tabOperationCurrent) != 0) {
		$diagramme['bloc']['idCurrent'] = $tabOperationCurrent->_id;
		$diagramme['bloc']['checkCurrent'] = "check";
	} else if (count($tabOperationExpect) != 0) {
		$diagramme['bloc']['idCurrent'] = $tabOperationExpect[0]->_id;
			$diagramme['bloc']['checkCurrent'] = "check";

	} else if (count($tabOperationDone) != 0) {
		$diagramme['bloc']['idCurrent'] = $tabOperationDone[0]->_id;
			$diagramme['bloc']['checkCurrent'] = "check";

	} else {
		$diagramme['bloc']['idCurrent'] = $date['id'];
			$diagramme['bloc']['checkCurrent'] = "check";

	}
} else {
	if (count($tabOperationCurrent) != 0) {
		$diagramme['bloc']['idCurrent'] = $tabOperationCurrent->_id;
		$diagramme['bloc']['type'] = "current";
		$diagramme['bloc']['checkCurrent'] = "check";
		$operation = $tabOperationCurrent;
	} else if (count($tabOperationExpect) != 0) {		
		$diagramme['bloc']['idCurrent'] = $tabOperationExpect[0]->_id;
		$diagramme['bloc']['type'] = "expect";
		$diagramme['bloc']['checkCurrent'] = "check";
		$operation = $tabOperationExpect[0];
	} else if (count($tabOperationDone) != 0) {
		$diagramme['bloc']['idCurrent'] = $tabOperationDone[0]->_id;
		$diagramme['bloc']['type'] = "done";
		$diagramme['bloc']['checkCurrent'] = "check";

		$operation = $tabOperationDone[0];
	}
}

// Construction du tableau pour construire le diagramme 
$diagramme['admission']['entree']['date'] = $sejour->entree_reelle == null ? $sejour->entree_prevue : $sejour->entree_reelle; 
$diagramme['admission']['sortie']['date'] = $sejour->sortie_reelle == null ? $sejour->sortie_prevue : $sejour->sortie_reelle;
$diagramme['admission']['sortie']['reelle'] = $sejour->sortie_reelle == null ? "sortie_prevue" : "sortie_reelle";
$diagramme['admission']['sortie']['mode_sortie'] = $sejour->mode_sortie;$diagramme['admission'];
if($today >= $sejour->entree_prevue && $today <= $sejour->sortie_prevue) {
	foreach ($affectations as $affectation) {
		if ($today >= $affectation->entree && $today <= $affectation->sortie) {
			$affectation->loadRefLit();
			$affectation->_ref_lit->loadCompleteView();
			$diagramme['hospitalise']['chambre'] = $affectation->_ref_lit->_view;
			$diagramme['hospitalise']['affectation'] = $affectation->_id;
		} 
	}
	if ($affectations == null) {
		$diagramme['hospitalise']['chambre'] = "Pas de chambre";
		$diagramme['hospitalise']['affectation'] = "";
	}
} else if($today < $sejour->entree_prevue) {
	  $affectation = $sejour->_ref_first_affectation;
		$affectation->loadRefLit();
		$affectation->_ref_lit->loadCompleteView();
		$diagramme['hospitalise']['chambre'] = $affectation->_ref_lit->_view;
		$diagramme['hospitalise']['affectation'] = $affectation->_id;
} else {
	$affectation = $sejour->_ref_last_affectation;
		$affectation->loadRefLit();
		$affectation->_ref_lit->loadCompleteView();
		$diagramme['hospitalise']['chambre'] = $affectation->_ref_lit->_view;
		$diagramme['hospitalise']['affectation'] = $affectation->_id;
}
if ($operation) {	
	$operation->loadRefsFwd();
	$diagramme['bloc']['vue'] = $operation->_view;
	$diagramme['bloc']['id'] = $operation->_id;
	$diagramme['bloc']['salle'] = $operation->entree_salle;
	$diagramme['bloc']['sortieSalle'] = $operation->sortie_salle;
	$diagramme['bloc']['salleReveil'] = $operation->entree_reveil;
	$diagramme['bloc']['sortieSalleReveil'] = $operation->sortie_reveil;
} else {
	$diagramme['bloc'] = null;
}

// Création du template
$smarty = new CSmartyDP();


$smarty->assign("sejour"		, $sejour);
$smarty->assign("operations", $operations);
$smarty->assign("affectations", $affectations);
$smarty->assign("diagramme",  $diagramme);
$smarty->display("vw_parcours.tpl");

?>