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

$date_start = CValue::getOrSession("date_start", CMbDT::date());
$date_end = CValue::getOrSession("date_end", $date_start);
$object_class = CValue::get("object_class");
$user_id = CValue::get("user_id");

$briss = CBrisDeGlace::loadBrisForOwnObject($user_id, array("CSejour"), $date_start, $date_end);
foreach ($briss as $_bris) {
  $_bris->loadRefUser()->loadRefFunction();
  $_bris->loadTargetObject();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("bris", $briss);
$smarty->display("inc_search_bris_by_user.tpl");