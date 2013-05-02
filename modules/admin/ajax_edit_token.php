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

CCanDo::checkEdit();

$token_id = CValue::getOrSession("token_id");

$token = new CViewAccessToken;
$token->load($token_id);
$token->loadRefsNotes();
$token->loadRefUser();

$smarty = new CSmartyDP();
$smarty->assign("token", $token);
$smarty->display("inc_edit_token.tpl");
