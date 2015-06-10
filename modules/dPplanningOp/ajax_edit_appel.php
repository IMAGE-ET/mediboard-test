<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage mvsante
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$appel_id   = CValue::get("appel_id");
$sejour_id  = CValue::get("sejour_id");
$type       = CValue::get("type");

//Chargement de l'appel
$appel = new CAppelSejour();
$appel->load($appel_id);
if (!$appel_id) {
  $appel->type      = $type;
  $appel->sejour_id = $sejour_id;
  $appel->datetime  = CMbDT::dateTime();
}

//Chargement du séjour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefsAppel($type, true);
$sejour->loadRefPatient();
$sejour->updateFormFields();

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("appel" , $appel);
$smarty->assign("sejour", $sejour);
$smarty->assign("type"  , $type);

$smarty->display("vw_edit_appel.tpl");
