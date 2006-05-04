<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('mediusers', 'groups') );

// Récupération des fonctions
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

foreach($listGroups as $key => $value) {
  $listGroups[$key]->loadRefs();
}

// Récupération du groupe selectionné
$usergroup = new CGroups;
$usergroup->load(mbGetValueFromGetOrSession("group_id", 0));

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('usergroup', $usergroup);
$smarty->assign('listGroups', $listGroups);

$smarty->display('vw_idx_groups.tpl');

?>