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

$sejour_id = CValue::post("sejour_id");
$sejour = new CSejour;
$sejour->load($sejour_id);
if (!$sejour->_id) {
  CAppUI::stepAjax("CMbObject-not-found", UI_MSG_ERROR, $sejour->_class, $sejour->_id);
}

$evenement = new CEvenementSSR();
$date_min = CMbDT::date($sejour->entree);
$date_max = CMbDT::date("+1 DAY", $sejour->sortie);

$where["sejour_id"] = "= '$sejour->_id'";
$where["debut"] = "NOT BETWEEN '$date_min' AND '$date_max'";

$evenements = $evenement->loadList($where);
foreach ($evenements as $_evenement) {
  $msg = $_evenement->delete();
  CAppUI::displayMsg($msg, "CEvenementSSR-msg-purge_hors_sejour");
}

echo CAppUI::getMsg();
CApp::rip();
