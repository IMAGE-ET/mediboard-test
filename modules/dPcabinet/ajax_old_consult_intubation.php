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

$consultations_anesth = array();

foreach ($patient->loadRefsConsultations() as $_consult) {
  $_consult->loadRefPraticien()->loadRefFunction();
  $_consult->loadRefPlageConsult(true);

  $consult_anesth = $_consult->loadRefConsultAnesth();
  if ($consult_anesth->_id && $consult_anesth->_id != $consult_anesth_id) {
    $consultations_anesth[] = $consult_anesth;
  }
}

$smarty = new CSmartyDP;
$smarty->assign("consultations_anesth", $consultations_anesth);
$smarty->assign("patient"             , $patient);
$smarty->display("inc_consult_anesth/vw_old_consult_anesth.tpl");
