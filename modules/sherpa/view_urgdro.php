<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sherpa
*/

global $can, $m;

$can->needsRead();

// Chargement du droit sélectionné
$droit = new CSpUrgDro();
$droit->load(mbGetValueFromGetOrSession("sel_numdos"));

// Récuperation des identifiants pour les filtres
$filter = new CSpUrgDro();
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
$droits = array();
if (count($where)) {
  $droits = $droit->loadList($where, $order, "0, 30");
}

// Désélection si le droit n'est pas dans la recherche
if (count($where) && !array_key_exists($droit->_id, $droits)) {
  $droit = new CSpOuvDro();
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$droit->_id && count($droits)) {
  $droit = reset($droits);
}

// Chargement de l'id400 associé
$droit->loadId400();
if ($droit->_ref_id400->_id) {
  $droit->_ref_id400->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("droits"  , $droits);
$smarty->assign("droit"   , $droit );

$smarty->display("view_urgdro.tpl");
?>