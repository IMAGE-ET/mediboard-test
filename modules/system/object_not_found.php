<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

list ($object_class, $object_id) = explode("-", mbGetValueFromGet("object_guid"));

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_class", $object_class);
$smarty->assign("object_id"   , $object_id);

$smarty->display("object_not_found.tpl");

?>