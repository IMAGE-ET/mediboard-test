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

$url_application = CAppUI::conf("vivalto url_application");

$smarty = new CSmartyDP;

$smarty->assign("url_application", $url_application);

$smarty->display("vw_dmi.tpl");
?>