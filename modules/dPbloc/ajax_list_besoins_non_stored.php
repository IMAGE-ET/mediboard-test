<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$types_ressources_ids = CValue::get("types_ressources_ids");
$type = CValue::get("type");

$types_ressources_ids = explode(",", $types_ressources_ids);

$besoins = array();

CMbArray::removeValue("", $types_ressources_ids);

foreach ($types_ressources_ids as $_type_ressource_id) {
  $besoin = new CBesoinRessource;
  $besoin->type_ressource_id = $_type_ressource_id;
  $type_ressource = $besoin->loadRefTypeRessource();
  $besoin->loadRefUsage();
  $besoins[] = $besoin;
}

$smarty = new CSmartyDP;

$smarty->assign("besoins", $besoins);
$smarty->assign("object_id", "");
$smarty->assign("type", $type);

$smarty->display("inc_list_besoins.tpl");
