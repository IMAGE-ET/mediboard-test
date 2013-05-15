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

$blocage_id = CValue::getOrSession("blocage_id");
$date_replanif = CValue::getOrSession("date_replanif");

$smarty = new CSmartyDP;

$smarty->assign("blocage_id"   , $blocage_id);
$smarty->assign("date_replanif", $date_replanif);

$smarty->display("vw_blocages.tpl");
