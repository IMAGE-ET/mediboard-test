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

$smarty = new CSmartyDP();
$smarty->display("vw_idx_keepers.tpl");