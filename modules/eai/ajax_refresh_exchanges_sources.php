<?php 
/**
 * Refresh Exchanges Source Actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid = CValue::getOrSession("actor_guid");

$actor = CMbObject::loadFromGuid($actor_guid);
$actor->loadRefsExchangesSources();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("actor", $actor);
$smarty->display($actor->_parent_class."_exchanges_source.tpl");

