<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("admin"));
require_once($AppUI->getModuleClass("mediusers", "mediusers"));
require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("mediusers", "discipline"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Récupération du user à ajouter/editer
$mediuserSel = new CMediusers;
$mediuserSel->load(mbGetValueFromGetOrSession("user_id"));

// Récupération des fonctions
$order = array("group_id", "text");
$functions = new CFunctions;
$functions = $functions->loadList(null, $order);

// Récupération des disciplines
$order = "text";
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList(null, $order);

// Récuperation des utilisateurs
foreach ($functions as $key => $function) {
  $functions[$key]->loadRefs();
}
  
// Récupération des profils
$where = array (
  "user_username" => "LIKE '>> %'"
);
$profiles = new CUser();
$profiles = $profiles->loadList($where);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('mediuserSel', $mediuserSel);
$smarty->assign('profiles', $profiles);
$smarty->assign('functions', $functions);
$smarty->assign('disciplines', $disciplines);

$smarty->display('vw_idx_mediusers.tpl');

?>