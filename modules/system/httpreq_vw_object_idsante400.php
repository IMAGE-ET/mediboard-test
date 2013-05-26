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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));
$smarty->assign("object", $object);
$smarty->display("inc_object_idsante400.tpl");
