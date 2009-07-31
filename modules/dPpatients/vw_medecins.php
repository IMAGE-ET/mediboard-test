<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;
$can->needsRead();

$dialog     = mbGetValueFromGet("dialog");
$medecin_id = mbGetValueFromGetOrSession("medecin_id");

// Parametre de tri
$order_way = mbGetValueFromGetOrSession("order_way", "DESC");
$order_col = mbGetValueFromGetOrSession("order_col", "ccmu");

// Récuperation du medecin sélectionné
$medecin = new CMedecin();
if(mbGetValueFromGet("new", 0) || $dialog) {
  $medecin->load(null);
  mbSetValueToSession("medecin_id", null);
}
else if ($medecin->load($medecin_id)) {
  $medecin->countPatients();
}

$code_default = str_pad(@$AppUI->user_prefs["DEPARTEMENT"], 2, "0", STR_PAD_LEFT);

// Récuperation des médecins recherchés
if($dialog) {
  $medecin_nom    = mbGetValueFromGet("medecin_nom"   , ""  );
  $medecin_prenom = mbGetValueFromGet("medecin_prenom", ""  );
  $medecin_dept   = mbGetValueFromGet("medecin_dept"  , $code_default);
  $medecin_type   = mbGetValueFromGet("medecin_type"  , "medecin");
} else {
  $medecin_nom    = mbGetValueFromGetOrSession("medecin_nom");
  $medecin_prenom = mbGetValueFromGetOrSession("medecin_prenom");
  $medecin_dept   = mbGetValueFromGetOrSession("medecin_dept", $code_default);
  $medecin_type   = mbGetValueFromGetOrSession("medecin_type", "medecin");
}

$where = array();
if ($medecin_nom   ) $where["nom"]      = "LIKE '$medecin_nom%'";
if ($medecin_prenom) $where["prenom"]   = "LIKE '$medecin_prenom%'";
if ($medecin_dept && $medecin_dept != "00") $where["cp"] = "LIKE '".$medecin_dept."___'";
if ($medecin_type)   $where["type"]     = "= '$medecin_type'";

if ($order_col == "cp") {
  $order = "cp $order_way, nom, prenom";
} else if ($order_col == "ville") {
	$order = "ville $order_way, nom, prenom";
} else {
	$order = "nom, prenom";
}

$medecins = new CMedecin();
$medecins = $medecins->loadList($where, $order, "0, 50");

$list_types = $medecin->_specs['type']->_locales;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dialog"     , $dialog);
$smarty->assign("nom"        , $medecin_nom);
$smarty->assign("prenom"     , $medecin_prenom);
$smarty->assign("departement", $medecin_dept);
$smarty->assign("type"       , $medecin_type);
$smarty->assign("medecins"   , $medecins);
$smarty->assign("medecin"    , $medecin);
$smarty->assign("list_types" , $list_types);

$smarty->assign("order_col"   , $order_col);
$smarty->assign("order_way"   , $order_way);

$smarty->display("vw_medecins.tpl");

?>