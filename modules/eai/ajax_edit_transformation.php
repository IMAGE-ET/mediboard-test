<?php
/**
 * Edit transformaiton rule EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$transformation_id = CValue::getOrSession("transformation_id");
$event_name        = CValue::getOrSession("event_name");

$transformation = new CEAITransformation();
$transformation->load($transformation_id);
$transformation->loadRefActor();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transformation", $transformation);
$smarty->assign("event_name"    , $event_name);

$smarty->display("inc_edit_transformation.tpl");