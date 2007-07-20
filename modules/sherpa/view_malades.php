<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $AppUI, $can, $m;

$can->needsRead();

// Chargement du patient sélectionné
$malnum = mbGetValueFromGetOrSession("malnum");
$malade = new CSpMalade;
if ($new = mbGetValueFromGet("new")) {
  $malade->load(null);
  mbSetValueToSession("malnum", null);
  mbSetValueToSession("selClass", null);
  mbSetValueToSession("selKey", null);
} else {
  $malade->load($malnum);
}

//Recuperation des identifiants pour les filtres
$filter = new CSpMalade;
$filter->malade_nom       = mbGetValueFromGetOrSession("malnom"       , ""       );
$filter->malade_prenom    = mbGetValueFromGetOrSession("malpre"    , ""       );
$malade_naissance 				= mbGetValueFromGetOrSession("naissance" , "off"    );
$malade_day       				= mbGetValueFromGetOrSession("Date_Day"  , date("d"));
$malade_month     				= mbGetValueFromGetOrSession("Date_Month", date("m"));
$malade_year      				= mbGetValueFromGetOrSession("Date_Year" , date("Y"));

$where        = array();

if ($filter->malade_nom) {
  $where["malnom"]                 = "LIKE '$filter->malade_nom%'";
}
if ($filter->malade_prenom) {
  $where["malpre"]                 = "LIKE '$filter->malade_prenom%'";
}
if ($malade_naissance == "on") $where["datnai"] = "= '$malade_day$malade_month$malade_year'";

$malades        = array();

$order = "malnom, malpre, datnai";
$mal = new CSpMalade();
if ($where) {
  $malades = $mal->loadList($where, $order, "0, 100");
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$malade->_id and count($malades) == 1) {
  $malade = reset($malades);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"         , $filter           			                    );
$smarty->assign("naissance", $malade_naissance           			  );
$smarty->assign("dateMal"        , "$malade_year-$malade_month-$malade_day"			);
$smarty->assign("malades"        , $malades                                 		);
$smarty->assign("malade"         , $malade                                  		);

$smarty->display("view_malades.tpl");
?>