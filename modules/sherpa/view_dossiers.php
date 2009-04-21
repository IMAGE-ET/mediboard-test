<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

// Chargement du dossier sélectionné
$dossier = new CSpDossier();
$dossier->load(mbGetValueFromGetOrSession("sel_numdos"));

// Récuperation des identifiants pour les filtres
$filter = new CSpDossier();
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->malnum = mbGetValueFromGetOrSession("malnum");


// Clauses where du filtre
$where = array();
if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}
if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum'";
}

$order = "numdos";

// Chargement des objets filtrés
$dossiers = array();
if (count($where)) {
  $dossiers = $dossier->loadList($where, $order, "0, 30");
}

// Désélection si le dossier n'est pas dans la recherche
if (count($where) && !array_key_exists($dossier->_id, $dossiers)) {
  $dossier = new CSpDossier();
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$dossier->_id && count($dossiers)) {
  $dossier = reset($dossiers);
}

// Chargement de l'id400 associé
$dossier->loadId400();
if ($dossier->_ref_id400->_id) {
  $dossier->_ref_id400->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("dossiers"  , $dossiers);
$smarty->assign("dossier"   , $dossier );

$smarty->display("view_dossiers.tpl");
?>