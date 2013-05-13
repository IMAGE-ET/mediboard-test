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

CCanDo::checkAdmin();

$date = CValue::getOrSession("date", CMbDT::date());

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->display("vw_idx_identito_vigilance.tpl");
