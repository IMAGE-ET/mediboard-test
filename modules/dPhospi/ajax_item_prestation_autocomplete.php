<?php 

/**
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$keywords = CValue::get("keywords");

$item_prestation = new CItemPrestation();

$where = array();
$ljoin = array();

$ljoin["prestation_ponctuelle"] = "prestation_ponctuelle.prestation_ponctuelle_id = item_prestation.object_id";

$where["prestation_ponctuelle.group_id"] = "= '" . CGroups::loadCurrent()->_id . "'";
$where["item_prestation.object_class"] = "= 'CPrestationPonctuelle'";

$matches = $item_prestation->getAutocompleteList($keywords, $where, null, $ljoin);

$smarty = new CSmartyDP("modules/system");

$smarty->assign("matches", $matches);
$smarty->assign("view_field", "nom");
$smarty->assign("template", "");
$smarty->assign("show_view", "");
$smarty->assign("input", $keywords);

$smarty->display("inc_field_autocomplete.tpl");