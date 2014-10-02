<?php

/**
 * Liste des hospitalisations en cours du tableau de bord
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date  = CValue::get("date", CMbDT::date());

$sejour = new CSejour();

$where = array();
$where["sejour.grossesse_id"] = "IS NOT NULL";
$where["sejour.entree"] = "<= '$date'";
$where["sejour.sortie"] = ">= '$date'";

$order = "sejour.entree DESC";

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, $order, null, null, null);

foreach ($listSejours as $_sejour) {
  $_sejour->loadRefGrossesse()->loadRefParturiente();
  $_sejour->loadRefCurrAffectation($date);
}

$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("listSejours", $listSejours);

$smarty->display("inc_tdb_hospitalisations.tpl");