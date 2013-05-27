<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$pat_id = CValue::getOrSession("pat_id");


// Chargement du dossier patient
$patient = new CPatient;
$patient->load($pat_id);

if ($patient->patient_id) {
  $patient->loadIPP();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"         , $patient );
$smarty->assign("hprim21installed", CModule::getActive("hprim21"));

$smarty->display("inc_ipp_form.tpl");