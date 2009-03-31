<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$item_type_id = mbGetValueFromGetOrSession('item_type_id');

$item_type = new CDailyCheckItemType;
$item_type->load($item_type_id);

$item_types_list = $item_type->loadGroupList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("item_type", $item_type);
$smarty->assign("item_types_list", $item_types_list);
$smarty->display("vw_daily_check_item_type.tpl");

?>