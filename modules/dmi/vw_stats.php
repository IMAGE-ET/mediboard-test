<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$chir_id = CValue::getOrSession("chir_id");
$date_min = CValue::getOrSession("_date_min");
$date_max = CValue::getOrSession("_date_max");
$_labo_id = CValue::getOrSession("_labo_id");
$group_by = CValue::getOrSession("group_by");
$septic = CValue::getOrSession("septic");

$interv = new COperation;
$interv->_date_min = $date_min;
$interv->_date_max = $date_max;
$interv->chir_id = $chir_id;

$list_chir = CAppUI::$user->loadPraticiens(PERM_EDIT);

$dmi = new CDMI;
$dmi->_labo_id = $_labo_id;

$dmi_line = new CPrescriptionLineDMI;
$dmi_line->septic = $septic;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("interv", $interv);
$smarty->assign("dmi", $dmi);
$smarty->assign("dmi_line", $dmi_line);
$smarty->assign("list_chir", $list_chir);
$smarty->assign("group_by", $group_by);
$smarty->display("vw_stats.tpl");
