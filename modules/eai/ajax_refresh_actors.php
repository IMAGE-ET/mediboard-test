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

$actor_class = CValue::get("actor_class");

/** @var CInteropActor $actor */
$actor = new $actor_class;
$actors = $actor->getObjects();

$count_actors       = 0;
$count_actors_actif = 0;
foreach ($actors as $_actors) {
  /** @var CInteropActor[] $_actors */
  $count_actors += count($_actors);
  foreach ($_actors as $_actor) {
    if ($_actor->actif) {
      $count_actors_actif++;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("parent_class"      , $actor_class);
$smarty->assign("actor"             , $actor);
$smarty->assign("actors"            , $actors);
$smarty->assign("count_actors"      , $count_actors);
$smarty->assign("count_actors_actif", $count_actors_actif);
$smarty->display("inc_actors.tpl");

