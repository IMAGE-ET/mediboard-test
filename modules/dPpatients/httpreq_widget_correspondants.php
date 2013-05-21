<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$patient_id = CValue::getOrSession("patient_id");
$widget_id = CValue::get("widget_id");

$patient = new CPatient();
$patient->load($patient_id);
if ($patient->_id) {
  $patient->loadRefsCorrespondants();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("widget_id", $widget_id);

$smarty->display("inc_widget_correspondants.tpl");
