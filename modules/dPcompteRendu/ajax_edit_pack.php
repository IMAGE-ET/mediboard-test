<?php

/**
 * Modification de pack de documents
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$pack_id = CValue::get("pack_id");
$user_id = CValue::getOrSession("user_id", CAppUI::$user->_id);

// Pour la création d'un pack, on affecte comme utilisateur celui de la session par défaut.
$user = CMediusers::get($user_id);

// Chargement du pack
$pack = new CPack;
$pack->load($pack_id);

// Accès aux packs de modèle de la fonction et de l'établissement
$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_group");

if ($pack->_id) {
  if ($pack->function_id && !$access_function) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  if ($pack->group_id && !$access_group) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  $pack->loadRefsNotes();
  $pack->loadBackRefs("modele_links", "modele_to_pack_id");
}
else {
  $pack->user_id = $user->_id;
}

$pack->loadRefOwner();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pack"           , $pack);
$smarty->assign("user_id"        , $user_id);
$smarty->assign("access_function", $access_function);
$smarty->assign("access_group"   , $access_group);

$smarty->display("inc_edit_pack.tpl"); 
