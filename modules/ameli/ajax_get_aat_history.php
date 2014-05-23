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

$patient_id = CValue::get('patient_id', 0);
$page = CValue::get('page', 0);
$header = CValue::get('header', 1);
$step = 20;

/** @var CPatient $patient */
$patient = CPatient::loadFromGuid("CPatient-$patient_id");
$aat_history = $patient->loadBackRefs('arret_travail', 'debut DESC', "$page, $step");
$total_aat = $patient->countBackRefs('arret_travail');
$smarty = new CSmartyDP();
$smarty->assign('aat_history', $aat_history);
$smarty->assign('page', $page);
$smarty->assign('total_aat', $total_aat);
$smarty->assign('view_full_history', 1);
$smarty->assign('patient_id', $patient_id);
if ($header) {
  $smarty->display('inc_full_aat_history.tpl');
}
else {
  $smarty->display('inc_aat_history.tpl');
}

