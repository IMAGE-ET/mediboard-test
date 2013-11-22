<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$forceChange  = CView::request("forceChange", "bool");
$lifeDuration = CView::request("lifeDuration", "bool");
CView::checkin();

$user = new CUser;
$user->load(CAppUI::$user->_id);
$user->updateSpecs();
$user->isLDAPLinked();

$password_info = (CAppUI::$user->_specs['_user_password']->minLength > 4) ?
  "Le mot de passe doit être composé d'au moins 6 caractères, comprenant des lettres et au moins un chiffre." :
  "Le mot de passe doit être composé d'au moins 4 caractères.";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user"        , $user);
$smarty->assign("forceChange" , $forceChange);
$smarty->assign("lifeDuration", $lifeDuration);
$smarty->assign("lifetime"    , CAppUI::conf("admin CUser password_life_duration"));
$smarty->assign("pwd_info",     $password_info);
$smarty->display("change_password.tpl");
