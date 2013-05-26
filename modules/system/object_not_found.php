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

@list ($object_class, $object_id) = explode("-", CValue::get("object_guid"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object_class", $object_class);
$smarty->assign("object_id"   , $object_id);
$smarty->display("object_not_found.tpl");
