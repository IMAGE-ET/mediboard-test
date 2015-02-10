<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Bloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkAdmin();

$purge_start_date = CMbDT::date();
$purge_limit      = 100;
$practitioners    = CMediusers::get()->loadPraticiens();

$smarty = new CSmartyDP();
$smarty->assign("purge_start_date", $purge_start_date);
$smarty->assign("purge_limit",      $purge_limit);
$smarty->assign("practitioners",    $practitioners);
$smarty->display("vw_purge_plagesop.tpl");