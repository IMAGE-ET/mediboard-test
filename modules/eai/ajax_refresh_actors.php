<?php 
/**
 * View interop receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_class_name = CValue::get("actor_class_name");

$actor = new $actor_class_name; 
$actors = $actor->getObjects();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("actor" , $actor);
$smarty->assign("actors", $actors);
$smarty->display("inc_actors.tpl");

?>