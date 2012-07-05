<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$pose_disp_vasc_id = CValue::get("pose_disp_vasc_id");

$pose = new CPoseDispositifVasculaire;
$pose->load($pose_disp_vasc_id);
$pose->loadRefSejour();

$check_lists = $pose->loadBackRefs("check_lists", "daily_check_list_id");
foreach($check_lists as $_check_list) {
  $_check_list->loadItemTypes();
  $_check_list->loadBackRefs('items', "daily_check_item_id");
  foreach($_check_list->_back['items'] as $_item) {
    $_item->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("pose", $pose);
$smarty->assign("sejour", $pose->_ref_sejour);
$smarty->assign("patient", $pose->_ref_sejour->loadRefPatient());
$smarty->display("print_check_list_pose_disp_vasc.tpl");
