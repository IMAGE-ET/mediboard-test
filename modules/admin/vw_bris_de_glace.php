<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$date_start = CValue::getOrSession("date_start", CMbDT::date());
$date_end = CValue::getOrSession("date_end", $date_start);

// smarty
$smarty = new CSmartyDP();
$smarty->assign("bris", new CBrisDeGlace());
$smarty->assign("date_start", $date_start);
$smarty->assign("date_end", $date_end);
$smarty->display("vw_bris_de_glace.tpl");