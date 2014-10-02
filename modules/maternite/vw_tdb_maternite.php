<?php

/**
 * Tableau de bord de la maternité
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date_tdb  = CValue::getOrSession("date_tdb", CMbDT::date());

$smarty = new CSmartyDP();

$smarty->assign("date_tdb", $date_tdb);
$smarty->assign("prec", CMbDT::date("-1 day", $date_tdb));
$smarty->assign("suiv", CMbDT::date("+1 day", $date_tdb));

$smarty->display("vw_tdb_maternite.tpl");