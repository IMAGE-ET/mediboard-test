<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleFile("dPboard", "inc_board");

$date = CValue::getOrSession("date", CMbDT::date());
$prec = CMbDT::date("-1 day", $date);
$suiv = CMbDT::date("+1 day", $date);
$vue  = CValue::getOrSession("vue2", CAppUI::pref("AFFCONSULT", 0));

global $smarty;

// Variables de templates
$smarty->assign("date", $date);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("vue",  $vue);

$smarty->display("vw_day.tpl");
