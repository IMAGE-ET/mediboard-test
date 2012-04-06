<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$naissance_id   = CValue::get("naissance_id");
$operation_id   = CValue::get("operation_id");
$provisoire     = CValue::get("provisoire", 0);
$sejour_id      = CValue::get("sejour_id");
$callback       = CValue::get("callback");

$constantes = new CConstantesMedicales;

$patient    = new CPatient;
$patient->naissance = mbDate();

$operation = new COperation();
$operation->load($operation_id);

$parturiente = null;
if ($operation->_id) {
  $parturiente = $operation->loadRefPatient();
}

if ($sejour_id) {
  $sejour = new CSejour;
  $sejour->load($sejour_id);
  $parturiente = $sejour->loadRefPatient();
}


$anonmymous = $parturiente ? is_numeric($parturiente->nom) : false;

$naissance  = new CNaissance;
if ($naissance_id) {
  $naissance->load($naissance_id);
  $patient = $naissance->loadRefSejourEnfant()->loadRefPatient();
  $constantes = $patient->getFirstConstantes();
}

else {
  if (!$provisoire) {
    $naissance->rang = $operation->countBackRefs("naissances") + 1;
    $naissance->heure = mbTime();
  }
  
  $naissance->operation_id = $operation_id;
  if (!$anonmymous) {
    $patient->nom = $parturiente->nom;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("naissance"  , $naissance);
$smarty->assign("patient"    , $patient);
$smarty->assign("constantes" , $constantes);
$smarty->assign("parturiente", $parturiente);
$smarty->assign("provisoire" , $provisoire);
$smarty->assign("sejour_id"  , $sejour_id);
$smarty->assign("callback"   , $callback);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("list_constantes", CConstantesMedicales::$list_constantes);

$smarty->display("inc_edit_naissance.tpl");
?>