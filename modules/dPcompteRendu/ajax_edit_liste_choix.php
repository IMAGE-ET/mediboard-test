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

// Utilisateurs disponibles
$user = CMediusers::get($user_id);

// Accès aux listes de choix de la fonction et de l'établissement
$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CListeChoix access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CListeChoix access_group");

// Liste sélectionnée
$liste = new CListeChoix();
$liste->user_id = $user->_id;
$liste->load($liste_id);
if ($liste->_id) {
  if ($liste->function_id && !$access_function) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  if ($liste->group_id && !$access_group) {
    CAppUI::redirect("m=system&a=access_denied");
  }
}

$liste->loadRefOwner();
$liste->loadRefModele();
$liste->loadRefsNotes();

$modeles = CCompteRendu::loadAllModelesFor($user->_id, "prat", null, "body");

$owners  = $user->getOwners();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modeles"         , $modeles);
$smarty->assign("owners"          , $owners);

$smarty->assign("access_function" , $access_function);
$smarty->assign("access_group"    , $access_group);

$smarty->assign("user"            , $user);
$smarty->assign("liste"           , $liste);

$smarty->display("inc_edit_liste_choix.tpl");
