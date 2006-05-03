<?php /* $Id: vw_idx_aides.php,v 1.11 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 1.11 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcompteRendu', 'aidesaisie'));
require_once( $AppUI->getModuleClass('mediusers', 'mediusers'));

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Class and fields
$classes = array (
    "Consultation" => array ("motif", "rques", "examen", "traitement", "compte_rendu"),
    "Operation" => array ("examen", "materiel", "convalescence", "compte_rendu"),
    "Patient" => array ("remarques"),
    "Antecedent" => array ("rques"),
    "Traitement" => array ("traitement"),
  );
  

// Liste des praticiens accessibles
$users = new CMediusers();
$users = $users->loadPraticiens(PERM_EDIT);

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
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('users', $users);
$smarty->assign('classes', $classes);
$smarty->assign('filter_user_id', $filter_user_id);
$smarty->assign('filter_class', $filter_class);
$smarty->assign('aides', $aides);
$smarty->assign('aide', $aide);

$smarty->display('vw_idx_aides.tpl');

?>