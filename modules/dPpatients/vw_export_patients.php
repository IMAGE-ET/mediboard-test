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

$praticien_id = CValue::postOrSession("praticien_id");
$step         = CValue::postOrSession("step", 100);
$start        = CValue::postOrSession("start", 0);
$directory    = CValue::postOrSession("directory");
$all_prats    = CValue::postOrSession("all_prats");

$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

if (!$praticien_id) {
  $praticien_id = array();
}

$smarty = new CSmartyDP();
$smarty->assign("praticiens", $praticiens);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("all_prats", $all_prats);
$smarty->assign("step", $step);
$smarty->assign("start", $start);
$smarty->assign("directory", $directory);
$smarty->display("vw_export_patients.tpl");
