<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();

$filter = new COperation();
$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date("-7 day"));
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());

$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id   = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);

$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

$function = new CFunctions();
$functions = $function->loadSpecialites();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"    , $filter);
$smarty->assign("bloc_id"   , $bloc_id);
$smarty->assign("listBlocs" , $listBlocs);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("functions" , $functions);
$smarty->assign("function_id" ,  CValue::getOrSession("function_id"));
$smarty->assign("praticien_id",  CValue::getOrSession("praticien_id"));

$smarty->display("vw_idx_materiel.tpl");
