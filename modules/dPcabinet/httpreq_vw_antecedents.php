<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

$sejour_id   = CValue::getOrSession("sejour_id");
$show_header = CValue::getOrSession("show_header", 0);

$sejour = new CSejour();
$sejour->load($sejour_id);

$userSel = CMediusers::get();

$patient = $sejour->loadRefPatient();
$patient->loadRefPhotoIdentite();

// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");
if (CModule::getActive("dPprescription")) {
  $smarty->assign("line", new CPrescriptionLineMedicament());
}
$smarty->assign("current_m", "dPcabinet");
$smarty->assign("sejour_id", $sejour->_id);
$smarty->assign("patient", $patient);
$smarty->assign("antecedent", new CAntecedent());
$smarty->assign("traitement", new CTraitement());
$smarty->assign("_is_anesth", "1");
$smarty->assign("userSel", $userSel);
$smarty->assign("today", CMbDT::date());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("sejour", $sejour);
$smarty->assign("show_header", $show_header);
$smarty->display("inc_ant_consult.tpl");
