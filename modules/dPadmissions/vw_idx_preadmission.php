<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Initialisation de variables

$order_way_pre = CValue::getOrSession("order_way_pre", "ASC");
$order_col_pre = CValue::getOrSession("order_col_pre", "heure");
$date          = CValue::getOrSession("date", CMbDT::date());

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date_demain"  , $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"         , $date);
$smarty->assign("order_way_pre", $order_way_pre);
$smarty->assign("order_col_pre", $order_col_pre);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->display("vw_idx_preadmission.tpl");
