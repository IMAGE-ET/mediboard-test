<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage mvsante
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

CCanDo::checkRead();
$appel_id   = CValue::getOrSession("appel_id");
$sejour_id  = CValue::getOrSession("sejour_id");
$type       = CValue::getOrSession("type");

//Chargement de l'appel
$appel = new CAppelSejour();
$appel->load($appel_id);
if (!$appel_id) {
  $appel->type      = $type;
  $appel->sejour_id = $sejour_id;
  $appel->user_id   = CMediusers::get()->_id;
  $appel->datetime  = CMbDT::dateTime();
}

//Chargement du séjour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->updateFormFields();
$sejour->loadRefsAppel($type);
if (!$appel_id || $sejour->_ref_appels_by_type[$type]->etat == "realise") {
  if ($sejour->_ref_appels_by_type[$type]->etat == "realise") {
    $appel = $sejour->_ref_appels_by_type[$type];
  }
  $sejour->loadRefsAppel($type, true);
  foreach ($sejour->_ref_appels_by_type as $type => $_appels) {
    foreach ($_appels as $_appel) {
      /* @var CAppelSejour $_appel*/
      $_appel->loadRefuser()->loadRefFunction();
    }
  }
}

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("appel"   , $appel);
$smarty->assign("sejour"  , $sejour);
$smarty->assign("type"    , $type);
$smarty->assign("appel_id", $appel_id);

$smarty->display("vw_edit_appel.tpl");
