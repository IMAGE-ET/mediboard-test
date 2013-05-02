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

$smarty = new CSmartyDP();
$smarty->assign("token_id", $token_id);
$smarty->display("vw_edit_tokens.tpl");
