<?php

/**
 * system
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$path = CValue::get("path");

$content_file = file_get_contents($path);

$smarty = new CSmartyDP;
$smarty->assign("content_file", $content_file);
$smarty->display("inc_vw_content_file.tpl");
?>