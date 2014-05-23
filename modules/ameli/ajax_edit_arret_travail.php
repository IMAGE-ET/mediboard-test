<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$consult_id = CValue::get('consult_id', 0);

$date = CMbDT::date();
$arret_travail = new CAvisArretTravail();
$aat_history = array();

if ($consult_id) {
  /** @var CConsultation $consult */
  $consult = CConsultation::loadFromGuid("CConsultation-$consult_id");
  /** @var CAvisArretTravail $arret_travail */
  $arret_travail = $consult->loadUniqueBackRef('arret_travail');
  if (!$arret_travail->_id) {
    $arret_travail->debut = $date;
    $arret_travail->consult_id = $consult_id;
    $arret_travail->patient_id = $consult->patient_id;

    if ($consult->date_at) {
      $arret_travail->date_accident = $consult->date_at;
    }
  }
  else {
    $arret_travail->loadRefMotif();
    $arret_travail->updateFormFields();
  }

  /** Load the history of the CavisArretTravail off the patient **/
  $patient = $consult->loadRefPatient();
  $aat_history = $patient->loadBackRefs('arret_travail', 'debut DESC', 5);
}

$smarty = new CSmartyDP();
$smarty->assign('arret_travail', $arret_travail);
$smarty->assign('date', $date);
$smarty->assign('aat_history', $aat_history);
$smarty->display('inc_edit_arret_travail.tpl');