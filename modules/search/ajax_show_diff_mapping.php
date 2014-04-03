<?php 

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$before = json_decode(stripslashes(CValue::get("before")), true);
$after  = json_decode(stripslashes(CValue::get("after")), true);

$smarty = new CSmartyDP();
$smarty->assign("before", $before);
$smarty->assign("after" , $after);
$smarty->display("vw_show_diff_mapping.tpl");