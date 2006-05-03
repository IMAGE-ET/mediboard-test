<?php /* $Id: vw_idx_functions.php,v 1.10 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 1.10 $
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('mediusers', 'functions') );
require_once( $AppUI->getModuleClass('mediusers', 'groups') );

// Récupération des fonctions
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

foreach($listGroups as $key => $value) {
  $listGroups[$key]->loadRefs();
  foreach($listGroups[$key]->_ref_functions as $key2 => $value2) {
    $listGroups[$key]->_ref_functions[$key2]->loadRefs();
  }
}

// Récupération de la fonction selectionnée
$userfunction = new CFunctions;
$userfunction->load(mbGetValueFromGetOrSession("function_id", 0));
$userfunction->loadRefsFwd();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('userfunction', $userfunction);
$smarty->assign('listGroups', $listGroups);

$smarty->display('vw_idx_functions.tpl');

?>