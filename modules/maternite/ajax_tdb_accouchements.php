<?php

/**
 * Liste des accouchements en cours du tableau de bord
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

$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("prec", CMbDT::date("-1 day", $date));
$smarty->assign("suiv", CMbDT::date("+1 day", $date));

$smarty->display("inc_tdb_accouchements.tpl");