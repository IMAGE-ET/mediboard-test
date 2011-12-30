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

$naissance_id = CValue::get("naissance_id");
$operation_id = CValue::get("operation_id");

$naissance  = new CNaissance;
$constantes = new CConstantesMedicales;
$patient    = new CPatient;
$patient->naissance = mbDate();

$operation = new COperation();
$operation->load($operation_id);
$parturiente = $operation->loadRefSejour()->loadRefGrossesse()->loadRefParturiente();

if ($naissance_id) {
  $naissance->load($naissance_id);
  $patient = $naissance->loadRefSejourEnfant()->loadRefPatient();
  $constantes = $patient->getFirstConstantes();
}
else {
  $naissance->operation_id = $operation_id;
}

$smarty = new CSmartyDP;

$smarty->assign("naissance"  , $naissance);
$smarty->assign("patient"    , $patient);
$smarty->assign("constantes" , $constantes);
$smarty->assign("parturiente", $parturiente);
$smarty->assign("list_constantes", CConstantesMedicales::$list_constantes);

$smarty->display("inc_edit_naissance.tpl");
?>