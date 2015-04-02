<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

global $prat;

// Chargement de l'utilisateur courant
$user = CMediusers::get();
$perm_fonct = CAppUI::pref("allow_other_users_board");

if (!$user->isPraticien() && !$user->isSecretaire()) {
  CAppUI::redirect("m=system&a=access_denied");
}

$praticiens = null;
$prat = new CMediusers();

// Si le user est un praticien
if ($user->_is_praticien) {
  if ($perm_fonct == "only_me") {
    $praticiens = array($user);
  }

  if ($perm_fonct == "same_function") {
    $praticiens = $user->loadPraticiens(PERM_READ, $user->function_id);
  }

  if ($perm_fonct == "write_right") {
    $praticiens = $user->loadPraticiens(PERM_EDIT);
  }

  if($perm_fonct == "read_right") {
    $praticiens = $user->loadPraticiens(PERM_READ);
  }

  $prat = $user;
}
else {
  if ($perm_fonct == "only_me" || $perm_fonct == "write_right") {
    $praticiens = $user->loadPraticiens(PERM_EDIT);
  }

  if($perm_fonct == "read_right") {
    $praticiens = $user->loadPraticiens(PERM_READ);
  }

  if ($perm_fonct == "same_function") {
    $praticiens = $user->loadPraticiens(PERM_READ, $user->function_id);
  }

}

$prat_selected = CValue::getOrSession("praticien_id");
if ($prat_selected) {
  $prat->load($prat_selected);
}
elseif($user->isPraticien()) {
  $prat = $user;
}
elseif (!$prat->_id && $user->isSecretaire() && count($praticiens) == 1) {
  $prat = reset($praticiens);
}

global $smarty;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"      , $user);
$smarty->assign("prat"      , $prat);
$smarty->assign("praticiens", $praticiens);

$smarty->display("inc_board.tpl");
