<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$ei_categorie_id = CValue::get("categorie_id");

$items = array();

if ($ei_categorie_id) {
  $where = array();
  $where["ei_categorie_id"] = " = '$ei_categorie_id'";

  $item  = new CEiItem;
  $items = $item->loadList($where);
}

$smarty = new CSmartyDP();
$smarty->assign("items", $items);
$smarty->display("ajax_list_items.tpl");
