<?php

/**
 * Liste des consultations du jour du tableau de bord
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

$consultation = new CConsultation();
$where = array();
$where["consultation.grossesse_id"] = "IS NOT NULL";
$where["plageconsult.date"] = "= '$date'";

$ljoin = array();
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

$order = "heure ASC";

/** @var CConsultation[] $listConsults */
$listConsults = $consultation->loadList($where, $order, null, null, $ljoin);

foreach ($listConsults as $_consult) {
  $_consult->loadRefPraticien();
  $_consult->loadRefGrossesse()->loadRefParturiente();
}

$smarty = new CSmartyDP();

$smarty->assign("date"        , $date);
$smarty->assign("listConsults", $listConsults);

$smarty->display("inc_tdb_consultations.tpl");