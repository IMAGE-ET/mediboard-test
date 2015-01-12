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
$event_class       = CValue::getOrSession("event_class");
$message_class     = CValue::getOrSession("message_class");

$transformation = new CEAITransformation();
$transformation->load($transformation_id);
$transformation->loadRefActor();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transformation", $transformation);
$smarty->assign("event_class"   , $event_class);
$smarty->assign("message_class" , $message_class);

$smarty->display("inc_edit_transformation.tpl");