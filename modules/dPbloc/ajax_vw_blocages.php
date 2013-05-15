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

$blocage_id = CValue::getOrSession("blocage_id");

$smarty = new CSmartyDP;

$smarty->assign("blocage_id", $blocage_id);

$smarty->display("inc_vw_blocages.tpl");
