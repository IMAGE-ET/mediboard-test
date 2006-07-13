<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients", "medecin"));

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$dialog     = mbGetValueFromGet("dialog");
$type       = mbGetValueFromGetOrSession("type", "_traitant");
$medecin_id = mbGetValueFromGetOrSession("medecin_id");

// Récuperation du medecin sélectionné
$medecin = new CMedecin();
if(mbGetValueFromGet("new", 0)) {
  $medecin->load(null);
  mbSetValueToSession("medecin_id", null);
}
else {
  $medecin->load($medecin_id);
}

// Récuperation des patients recherchés
if($dialog) {
  $medecin_nom    = mbGetValueFromGet("medecin_nom"   , ""  );
  $medecin_prenom = mbGetValueFromGet("medecin_prenom", ""  );
  $medecin_dept   = mbGetValueFromGet("medecin_dept"  , "17");
} else {
  $medecin_nom    = mbGetValueFromGetOrSession("medecin_nom"       );
  $medecin_prenom = mbGetValueFromGetOrSession("medecin_prenom"    );
  $medecin_dept   = mbGetValueFromGetOrSession("medecin_dept", "17");
}

$where = array();
if ($medecin_nom   ) $where[] = "nom LIKE '$medecin_nom%'";
if ($medecin_prenom) $where[] = "prenom LIKE '$medecin_prenom%'";
if ($medecin_dept != "00") $where[] = "cp LIKE '".$medecin_dept."___'";

$medecins = new CMedecin();
$medecins = $medecins->loadList($where, "nom, prenom", "0, 100");

// Création du template
require_once($AppUI->getSystemClass("smartydp" ));
$smarty = new CSmartyDP(1);

$smarty->assign("dialog"     , $dialog);
$smarty->assign("type"       , $type);
$smarty->assign("nom"        , $medecin_nom);
$smarty->assign("prenom"     , $medecin_prenom);
$smarty->assign("departement", $medecin_dept);
$smarty->assign("medecins"   , $medecins);
$smarty->assign("medecin"    , $medecin);

$smarty->display("vw_medecins.tpl");

?>