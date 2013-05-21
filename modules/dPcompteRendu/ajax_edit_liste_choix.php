<?php

/**
 * Modification de liste de choix
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_id  = CValue::getOrSession("user_id");
$liste_id = CValue::getOrSession("liste_id");

if (!$user_id) {
  $user_id = CAppUI::$user->_id;
}

// Utilisateurs disponibles
$user = CMediusers::get($user_id);
$users = $user->loadUsers(PERM_EDIT);

// Functions disponibles
$func = new CFunctions();
$funcs = $func->loadSpecialites(PERM_EDIT);

// Etablissements disponibles
$etabs = array(CGroups::loadCurrent());

// Liste sélectionnée
$liste = new CListeChoix();
$liste->user_id = $user->_id;
$liste->load($liste_id); 
$liste->loadRefOwner();
$liste->loadRefModele();
$liste->loadRefsNotes();

$modeles = CCompteRendu::loadAllModelesFor($user->_id, "prat", null, "body");

$owners  = $user->getOwners();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("users"  , $users);
$smarty->assign("modeles", $modeles);
$smarty->assign("owners" , $owners);

$smarty->assign("prats"  , $users);
$smarty->assign("funcs"  , $funcs);
$smarty->assign("etabs"  , $etabs);

$smarty->assign("user"   , $user );
$smarty->assign("liste"  , $liste);

$smarty->display("inc_edit_liste_choix.tpl");
