<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date    = CValue::getOrSession("date", CMbDT::date());
$bloc_id = CValue::getOrSession("bloc_id");

$modif_operation = CCanDo::edit() || $date >= CMbDT::date();
$blocs_list = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id) && count($blocs_list)) {
  $bloc = reset($blocs_list);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"                 , $date);
$smarty->assign("hour"                 , CMbDT::time());
$smarty->assign("modif_operation"      , $modif_operation);
$smarty->assign("blocs_list"           , $blocs_list);
$smarty->assign("bloc"                 , $bloc);

$smarty->assign("isImedsInstalled"     , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_reveil.tpl");
