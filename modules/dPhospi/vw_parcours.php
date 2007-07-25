<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Poiron Yohann
*/

global $AppUI, $can, $m, $g;

$today = mbDateTime();
$sejour_id = mbGetValueFromGetOrSession("sejour_id",0);

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

foreach ($datesOperation as &$date) {
	$estEnSalle = $date['entree_salle'] && $date['sortie_salle'] == null;
	$estSortieSalle = $date['sortie_salle'] && $date['entree_reveil'] == null;
	$estSalleReveil = $date['sortie_salle'] && $date['entree_reveil'];
	$estSortieSalleReveil = $date['entree_salle'] && $date['sortie_salle'] && $date['entree_reveil'] && $date['sortie_reveil'];
	
	if ($date['entree_salle'] == null) {
		$tabOperationExpect[] = $operations[$date['id']];
		$date['typeOperation'] = "nonEffectue";
	}
	if ($estEnSalle || $estSortieSalle || $estSalleReveil) {
		$tabOperationCurrent = $operations[$date['id']];
		$date['typeOperation'] = "enCours";
	}
	if ($estSortieSalleReveil) {
		$tabOperationDone[] = $operations[$date['id']];
		$date['typeOperation'] = "effectue";
	}
}

uasort($tabOperationExpect, 'cmp_dateDesc');
uasort($tabOperationDone, 'cmp_dateAsc');

$diagramme = array();

$operation = null;
if (count($tabOperationCurrent) != 0) {
	$diagramme['bloc']['type'] = "current";
	$operation = $tabOperationCurrent;
} else if (count($tabOperationExpect) != 0) {
	$diagramme['bloc']['type'] = "expect";
	$operation = $tabOperationExpect[0];
} else if (count($tabOperationDone) != 0) {
	$diagramme['bloc']['type'] = "done";
	$operation = $tabOperationDone[0];
} else {
	$diagramme['bloc']['type'] = "none";
	$operation = null;
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