<?php /* $Id: ficheEi.class.php 10176 2010-09-27 12:35:33Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision: 10176 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
