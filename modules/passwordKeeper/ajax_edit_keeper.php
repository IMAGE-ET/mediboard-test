<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$password_keeper_id = CValue::getOrSession("password_keeper_id");
$_passphrase        = CValue::get("passphrase");
$deletion           = CValue::get("deletion");

$user   = CMediusers::get();
$keeper = new CPasswordKeeper();
$keeper->load($password_keeper_id);

if ($keeper->_id && $keeper->user_id != $user->_id) {
  $msg = "Vous n'avez pas droit d'accéder à ce trousseau.";

  // Template d'erreur
  $smarty = new CSmartyDP();
  $smarty->assign("msg", $msg);
  $smarty->display("inc_bad_keeper.tpl");
  CApp::rip();
}

// Second passage, après avoir saisi la phrase de passe
if ($keeper->_id && $_passphrase) {
  if (!$keeper->testSample($_passphrase)) {
    $msg = "Phrase de passe incorrecte.";

    // Template d'erreur
    $smarty = new CSmartyDP();
    $smarty->assign("msg", $msg);
    $smarty->display("inc_bad_keeper.tpl");
    CApp::rip();
  }

  // Ecrit la phrase de passe en session
  CValue::setSessionAbs("passphrase", $_passphrase);
}

$smarty = new CSmartyDP();
$smarty->assign("keeper"     , $keeper);
$smarty->assign("user"       , $user);
$smarty->assign("_passphrase", $_passphrase);
$smarty->assign("deletion"   , $deletion);
$smarty->display("inc_edit_keeper.tpl");