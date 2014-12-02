<?php 

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$patient_id = CValue::get("patient_id");
$consult_anesth_id = CValue::get("consult_anesth_id");
$patient = new CPatient();
$patient->load($patient_id);

$dossiers_anesth = array();

foreach ($patient->loadRefsConsultations() as $_consult) {
  foreach ($_consult->loadRefsDossiersAnesth() as $_dossier) {
    if ($_dossier->_id != $consult_anesth_id) {
      $_dossier->_ref_consultation = $_consult;
      $_consult->loadRefPraticien()->loadRefFunction();
      $_consult->loadRefPlageConsult(true);
      $dossiers_anesth[] = $_dossier;
    }
  }
}

$smarty = new CSmartyDP;
$smarty->assign("dossiers_anesth", $dossiers_anesth);
$smarty->assign("patient", $patient);
$smarty->display("inc_consult_anesth/vw_old_consult_anesth.tpl");
