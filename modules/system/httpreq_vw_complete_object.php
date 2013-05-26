<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

$object->loadComplete();

global $can;
$can->read = $object->canRead();
$can->edit = $object->canEdit();
$can->needsRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("object", $object);
$template =   $object->makeTemplatePath("complete"); 
$smarty->display("../../$template");
