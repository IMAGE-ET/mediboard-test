<?php 
/**
 * Details interop receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid       = CValue::getOrSession("actor_guid");
$actor_class_name = CValue::getOrSession("actor_class_name");

// Chargement de l'acteur d'interopérabilité
if ($actor_class_name) {
  $actor = new $actor_class_name;
  $actor->updateFormFields();
} else {
  if ($actor_guid) {
    $actor = CMbObject::loadFromGuid($actor_guid);
    if ($actor->_id) {
      $actor->loadRefGroup();
      $actor->loadRefsExchangesSources();
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("actor" , $actor);
$smarty->display("inc_actor.tpl");

?>