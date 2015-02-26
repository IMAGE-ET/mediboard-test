<?php 

/**
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$praticien_id = CValue::getOrSession("praticien_id");
$step = CValue::getOrSession("step", 100);
$start = CValue::getOrSession("start", 0);
$directory = CValue::getOrSession("directory");

$praticien = new CMediusers();
$praticien->load($praticien_id);

$praticiens = $praticien->loadPraticiens();

$smarty = new CSmartyDP();

$smarty->assign("praticiens", $praticiens);
$smarty->assign("praticien", $praticien);
$smarty->assign("step", $step);
$smarty->assign("start", $start);
$smarty->assign("directory", $directory);

$smarty->display("vw_export_patients.tpl");
