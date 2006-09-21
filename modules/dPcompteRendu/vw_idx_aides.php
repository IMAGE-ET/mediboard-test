<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Class and fields
$classes = array (
  "CConsultation" => array ("motif", "rques", "examen", "traitement", "compte_rendu"),
  "CConsultAnesth" => array ("tabac", "oenolisme", "etatBucco" , "conclusion", "premedication", "prepa_preop"),
  "COperation" => array ("examen", "materiel", "convalescence", "compte_rendu"),
  "CPatient" => array ("remarques"),
  "CAntecedent" => array ("rques"),
  "CTraitement" => array ("traitement"),
  "CTechniqueComp" => array("technique"),
  "CExamComp" => array("examen")
);  

// Liste des praticiens accessibles
$users = new CMediusers();
$users = $users->loadUsers(PERM_READ);

$user_id = array_key_exists($AppUI->user_id, $users) ? $AppUI->user_id : null;

// Filtres sur la liste d'aides
$where = null;

$filter_user_id = mbGetValueFromGetOrSession("filter_user_id", $user_id);
if ($filter_user_id) {
  $where["user_id"] = "= '$filter_user_id'";
} else {
  $inUsers = array();
  foreach($users as $key => $value) {
    $inUsers[] = $key;
  }
  $where ["user_id"] = "IN (".implode(",", $inUsers).")";
}

$filter_class = mbGetValueFromGetOrSession("filter_class");
if ($filter_class) {
  $where["class"] = "= '$filter_class'";
}

$order = array("user_id", "class", "field", "name");
$aides = new CAideSaisie();
$aides = $aides->loadList($where, $order);
foreach($aides as $key => $aide) {
  $aides[$key]->loadRefsFwd();
}

// Aide sélectionnée
$aide_id = mbGetValueFromGetOrSession("aide_id");
$aide = new CAideSaisie();
$aide->load($aide_id); 
$aide->loadRefs();

if (!$aide_id) {
  $aide->user_id = $user_id;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("users"         , $users);
$smarty->assign("classes"       , $classes);
$smarty->assign("filter_user_id", $filter_user_id);
$smarty->assign("filter_class"  , $filter_class);
$smarty->assign("aides"         , $aides);
$smarty->assign("aide"          , $aide);

$smarty->display("vw_idx_aides.tpl");

?>