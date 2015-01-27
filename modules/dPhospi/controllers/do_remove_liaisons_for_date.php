<?php 

/**
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$date      = CValue::post("date");
$sejour_id = CValue::post("sejour_id");

if (!$date) {
  CApp::rip();
}

$liaison = new CItemLiaison();
$liaison->date = $date;
$liaison->sejour_id = $sejour_id;

foreach ($liaison->loadMatchingList() as $_liaison) {
  $_liaison->delete();
}
