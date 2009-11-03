<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

$dialog     = CValue::get("dialog");
$medecin_id = CValue::getOrSession("medecin_id");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", "ccmu");

// Récuperation du medecin sélectionné
$medecin = new CMedecin();
if(CValue::get("new", 0) || $dialog) {
  $medecin->load(null);
  CValue::setSession("medecin_id", null);
}
else if ($medecin->load($medecin_id)) {
  $medecin->countPatients();
}

$code_default = str_pad(CAppUI::pref("DEPARTEMENT"), 2, "0", STR_PAD_LEFT);

// Récuperation des médecins recherchés
if($dialog) {
  $medecin_nom    = CValue::get("medecin_nom"   , ""  );
  $medecin_prenom = CValue::get("medecin_prenom", ""  );
  $medecin_dept   = CValue::get("medecin_dept"  , $code_default);
  $medecin_type   = CValue::get("medecin_type"  , "medecin");
} else {
  $medecin_nom    = CValue::getOrSession("medecin_nom");
  $medecin_prenom = CValue::getOrSession("medecin_prenom");
  $medecin_dept   = CValue::getOrSession("medecin_dept", $code_default);
  $medecin_type   = CValue::getOrSession("medecin_type", "medecin");
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