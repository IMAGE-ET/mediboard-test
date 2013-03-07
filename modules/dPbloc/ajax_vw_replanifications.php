<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$date_replanif = CValue::getOrSession("date_replanif", CMbDT::date());

$smarty = new CSmartyDP;

$smarty->assign("date_replanif", $date_replanif);

$smarty->display("inc_vw_replanifications.tpl");

?>