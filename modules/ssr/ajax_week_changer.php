<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());

$planning = new CPlanningWeek($date);
$next_week = CMbDT::date("+1 week", $date);
$prev_week = CMbDT::date("-1 week", $date);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->assign("next_week", $next_week);
$smarty->assign("prev_week" , $prev_week);
$smarty->display("inc_week_changer.tpl");
