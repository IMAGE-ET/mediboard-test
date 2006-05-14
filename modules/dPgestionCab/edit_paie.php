<?php /* $Id: edit_compta.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPgestionCab', 'fichePaie') );
require_once( $AppUI->getModuleClass('mediusers') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$user = new CMediusers();
$user->load($AppUI->user_id);

$user_id = mbGetValueFromGetOrSession("user_id", $user->user_id);
$fiche_paie_id = mbGetValueFromGetOrSession("fiche_paie_id", null);

$user->load($user_id);

$paramsPaie = new CParamsPaie();
$paramsPaie->loadFromUser($user_id);

$fichePaie = new CFichePaie();
$fichePaie->load($fiche_paie_id);
if(!$fichePaie->fiche_paie_id) {
  $fichePaie->debut = mbDate();
  $fichePaie->fin = mbDate();
}

$listeFiches = new CFichePaie();
$where = array();
$where["params_paie_id"] = "= $paramsPaie->params_paie_id";
$order = "debut DESC";
$listeFiches = $listeFiches->loadList($where, $order);

$listUsers = $user->loadUsers(PERM_EDIT);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('user', $user);
$smarty->assign('fichePaie', $fichePaie);
$smarty->assign('paramsPaie', $paramsPaie);
$smarty->assign('listFiches', $listeFiches);
$smarty->assign('listUsers', $listUsers);

$smarty->display('edit_paie.tpl');
?>