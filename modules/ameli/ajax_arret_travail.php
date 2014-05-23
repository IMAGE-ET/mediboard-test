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

$arret_travail = new CAvisArretTravail();

if ($consult_id) {
  /** @var CConsultation $consult */
  $consult = CConsultation::loadFromGuid("CConsultation-$consult_id");
  /** @var CAvisArretTravail $arret_travail */
  $arret_travail = $consult->loadUniqueBackRef('arret_travail');
  if ($arret_travail->_id) {
    $arret_travail->loadRefMotif();
    $arret_travail->updateFormFields();
  }
}

$smarty = new CSmartyDP();
$smarty->assign('arret_travail', $arret_travail);
$smarty->assign('consult_id', $consult_id);
$smarty->display('inc_arret_travail.tpl');
