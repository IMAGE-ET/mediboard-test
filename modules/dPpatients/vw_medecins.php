<?php /* $Id: vw_medecins.php,v 1.9 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 1.9 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPpatients', 'medecin') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$dialog = dPgetParam($_GET, "dialog");

$type = mbGetValueFromGetOrSession("type", '_traitant');
$medecin_id = mbGetValueFromGetOrSession("medecin_id");

// Récuperation du medecin sélectionné
$medecin = new CMedecin();
if(dPgetParam($_GET, "new", 0)) {
  $medecin->load(NULL);
  mbSetValueToSession("medecin_id", null);
}
else {
  $medecin->load($medecin_id);
  //$medecin->loadRefs();
}

// Récuperation des patients recherchés
if($dialog) {
  $medecin_nom    = dPgetParam($_GET, "medecin_nom", '');
  $medecin_prenom = dPgetParam($_GET, "medecin_prenom", '');
  $medecin_dept = dPgetParam($_GET, "medecin_dept", "17");
} else {
  $medecin_nom    = mbGetValueFromGetOrSession("medecin_nom"   );
  $medecin_prenom = mbGetValueFromGetOrSession("medecin_prenom");
  $medecin_dept = mbGetValueFromGetOrSession("medecin_dept", "17");
}

$where = array();
if ($medecin_nom   ) $where[] = "nom LIKE '$medecin_nom%'";
if ($medecin_prenom) $where[] = "prenom LIKE '$medecin_prenom%'";
if ($medecin_dept != "00") $where[] = "cp LIKE '".$medecin_dept."___'";

$medecins = new CMedecin();
$medecins = $medecins->loadList($where, "nom, prenom", "0, 100");

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('dialog', $dialog);
$smarty->assign('type', $type);
$smarty->assign('nom', $medecin_nom);
$smarty->assign('prenom', $medecin_prenom);
$smarty->assign('departement', $medecin_dept);
$smarty->assign('medecins', $medecins);
$smarty->assign('medecin', $medecin);

$smarty->display('vw_medecins.tpl');
?>