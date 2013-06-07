<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::admin();

$praticien_id = CValue::get("praticien_id");
$date         = CValue::get("date", CMbDT::date("+5 year"));
$limit        = CValue::get("limit", 100);

$plage = new CPlageconsult();

$where = array();
if ($praticien_id) {
  $where["plageconsult.chir_id"] = "= '$praticien_id'";
}
$where["plageconsult.date"] = "> '$date'";

$count = $plage->countList($where);
CAppUI::setMsg("'$count' plages à supprimer", UI_MSG_OK);

/** @var CPlageconsult[] $listPlages */
$listPlages = $plage->loadList($where, null, $limit);

foreach ($listPlages as $_plage) {
  if ($msg = $_plage->delete()) {
    CAppUI::setMsg("Plage non supprimée", UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg("Plage supprimée", UI_MSG_OK);
  }
}

echo CAppUI::getMsg();