<?php 

/**
 * $Id$
 *  
 * @category Hospitalisation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$sous_item_id       = CValue::get("sous_item_id");
$item_prestation_id = CValue::get("item_prestation_id");

$sous_item = new CSousItemPrestation();
$sous_item->load($sous_item_id);

if (!$sous_item->_id) {
  $sous_item->item_prestation_id = $item_prestation_id;
}

$sous_item->loadRefItemPrestation();

$smarty = new CSmartyDP();

$smarty->assign("sous_item", $sous_item);

$smarty->display("inc_edit_sous_item.tpl");