<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

if (empty($_SERVER["HTTPS"])) {
  $msg = "Vous devez utiliser le protocole HTTPS pour utiliser ce module.";
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

CCanDo::checkAdmin();

$password_id = CValue::getOrSession("password_id");

$password = new CPasswordEntry();
$password->load($password_id);

// Déchiffrement
$revealed = $password->getPassword();

$smarty = new CSmartyDP();
$smarty->assign("revealed", $revealed);
$smarty->display("inc_revealed.tpl");