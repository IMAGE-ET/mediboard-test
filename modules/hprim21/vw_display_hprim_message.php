<?php

/**
 * Affichage des messages Hprim21
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$message = CValue::getOrSession("message");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("message", $message);
$smarty->display("vw_display_hprim_message.tpl");
