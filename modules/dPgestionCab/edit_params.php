<?php /* $Id: edit_compta.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPgestionCab', 'paramsPaie') );
require_once( $AppUI->getModuleClass('mediusers') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$user = new CMediusers();
$user->load($AppUI->user_id);

$user_id = mbGetValueFromGetOrSession("user_id", $user->user_id);

$user->load($user_id);

$paramsPaie = new CParamsPaie();
$paramsPaie->loadFromUser($user_id);
$paramsPaie->loadRefsFwd();

$listUsers = $user->loadUsers(PERM_EDIT);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('user', $user);
$smarty->assign('paramsPaie', $paramsPaie);
$smarty->assign('listUsers', $listUsers);

$smarty->display('edit_params.tpl');
?>