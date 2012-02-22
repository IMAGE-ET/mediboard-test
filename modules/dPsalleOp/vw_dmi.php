<?php

/**
 * dmi
 *  
 * @category dmi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$operation_id    = CValue::getOrSession("operation_id");
$url_application = CAppUI::conf("vivalto url_application");
$url_application .= "?interventionMB=$operation_id";

$smarty = new CSmartyDP;

$smarty->assign("url_application", $url_application);

$smarty->display("vw_dmi.tpl");
?>