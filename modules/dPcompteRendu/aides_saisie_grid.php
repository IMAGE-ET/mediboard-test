<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$object_class = CValue::get("object_class");
$user_id      = CValue::get("user_id");
$property     = CValue::get("property");

$object = new $object_class;
$object->loadAides($user_id);

// Tableau de depend value
@list($depend_field_1, $depend_field_2) = $object->_specs[$property]->helped;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("property", $property);
$smarty->assign("depend_field_1", $depend_field_1);
$smarty->assign("depend_field_2", $depend_field_2);

$smarty->display("aides_saisie_grid.tpl");