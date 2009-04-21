<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

// Chargement du sejour sélectionné
$entccam = new CSpEntCCAM();
if ("0" == $entccam->idinterv = mbGetValueFromGetOrSession("sel_idinterv")) {
  $entccam->numdos = mbGetValueFromGetOrSession("sel_numdos");
}

$entccam->loadMatchingObject();

// Récuperation des identifiants pour les filtres
$filter = new CSpEntCCAM();
$filter->idinterv = mbGetValueFromGetOrSession("idinterv");
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->malnum = mbGetValueFromGetOrSession("malnum");
$day   = mbGetValue(mbGetValueFromGetOrSession("Day"  ), "__");
$month = mbGetValue(mbGetValueFromGetOrSession("Month"), "__");
$year  = mbGetValue(substr(mbGetValueFromGetOrSession("Year"), 2, 2), "__");
$filter->_date = "$day/$month/$year";
$filter->pracod = mbGetValueFromGetOrSession("pracod");
$filter->salcod = mbGetValueFromGetOrSession("salcod");

// Clauses du filtre
$where = array();
if ($filter->idinterv) {
  $where[] = "ASCII(`idinterv`) = '$filter->idinterv'";
}

if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}

if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum%'";
}

if ($filter->_date != "__/__/__") {
  $where[] = "ASCII(`debint`) LIKE '$filter->_date%'";
}

if ($filter->pracod) {
  $where["pracod"] = "LIKE '$filter->pracod%'";
}

if ($filter->salcod) {
  $where["litcod"] = "LIKE '$filter->salcod%'";
}

$order = "idinterv";

$entsccam = $filter->loadList($where, $order, "0,30");

// Désélection si l'entête n'est pas dans la recherche
if (count($where) && !array_key_exists($entccam->_id, $entsccam)) {
  $entccam = new CSpSejMed();
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$entccam->_id && count($entsccam)) {
  $entccam = reset($entsccam);
}

// Chargement de l'id400 associé
$entccam->loadId400();
if ($entccam->_ref_id400->_id) {
  $entccam->_ref_id400->loadRefsFwd();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"  , str_replace("_", "", "$year-$month-$day"));
$smarty->assign("filter"   , $filter);
$smarty->assign("entccam"  , $entccam);
$smarty->assign("entsccam" , $entsccam);

$smarty->display("view_entccam.tpl");
?>