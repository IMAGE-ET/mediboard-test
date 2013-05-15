<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$now       = CMbDT::date();

$filter = new COperation;
$filter->_date_min = CValue::get("_date_min"    , "$now");
$filter->_date_max = CValue::get("_date_max"    , "$now");

$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id   = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"    , $filter);
$smarty->assign("bloc_id"   , $bloc_id);
$smarty->assign("listBlocs" , $listBlocs);

$smarty->display("vw_idx_materiel.tpl");
