<?php
/**
 * View interop actors EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$router = new CEAIRouter();
$routers = $router->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("routers", $routers);

$smarty->display("vw_routers.tpl");
