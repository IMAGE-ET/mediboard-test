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

$token = new CViewAccessToken;
$tokens = $token->loadList();

$smarty = new CSmartyDP();
$smarty->assign("tokens", $tokens);
$smarty->display("inc_list_tokens.tpl");
