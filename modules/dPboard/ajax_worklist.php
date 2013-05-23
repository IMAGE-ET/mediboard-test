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

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");
// Récupération des paramètres
$chirSel   = CValue::getOrSession("chirSel");
$date      = CValue::getOrSession("date", CMbDT::date());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"   , $date);
$smarty->assign("chirSel", $chirSel);

$smarty->display("inc_worklist.tpl");
