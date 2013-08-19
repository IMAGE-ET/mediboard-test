<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$observation_id = CValue::get("observation_id");
$sejour_id      = CValue::get("sejour_id");
$user_id        = CValue::get("user_id");

$observation = new CObservationMedicale;

if ($observation_id) {
  $observation->load($observation_id);
}
else {
  $observation->sejour_id = $sejour_id;
  $observation->user_id   = $user_id;
}

$smarty = new CSmartyDP;

$smarty->assign("observation", $observation);

$smarty->display("inc_observation.tpl");
