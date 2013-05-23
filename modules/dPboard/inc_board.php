<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

global $prat;

// Chargement de l'utilisateur courant
$user = CMediusers::get();

if (!$user->isPraticien() && !$user->isSecretaire()) {
  CAppUI::redirect("m=system&a=access_denied");
}

$praticiens = null;
$prat = new CMediusers();

// Si le user est secretaire
if ($user->_is_secretaire) {
  // Chargement de la liste de praticien
  $praticiens = $user->loadPraticiens(PERM_EDIT);
  $prat->load(CValue::getOrSession("praticien_id"));
}

// Si le user est un praticien
if ($user->_is_praticien) {
  $prat = $user;
}

global $smarty;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"      , $user);
$smarty->assign("prat"      , $prat);
$smarty->assign("praticiens", $praticiens);

$smarty->display("inc_board.tpl");
