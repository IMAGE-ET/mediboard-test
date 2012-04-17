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
$sejour_id      = CValue::get("sejour_id");
$operation_id   = CValue::get("operation_id");
$provisoire     = CValue::get("provisoire", 0);
$sejour_id      = CValue::get("sejour_id");
$callback       = CValue::get("callback");

$constantes = new CConstantesMedicales;

$patient    = new CPatient;
$patient->naissance = mbDate();

$sejour = new CSejour;
$sejour->load($sejour_id);
$parturiente = $sejour->loadRefPatient();

$anonmymous = $parturiente ? is_numeric($parturiente->nom) : false;

$naissance  = new CNaissance;

if ($naissance_id) {
  $naissance->load($naissance_id);
  
  // Quand la naissance existe, le praticien à modifier est
  // celui du séjour de l'enfant.
  $sejour = $naissance->loadRefSejourEnfant();
  $patient = $sejour->loadRefPatient();
  $constantes = $patient->getFirstConstantes();
}
else {
  if (!$provisoire) {
    $naissance->rang = $sejour->countBackRefs("naissances") + 1;
    $naissance->heure = mbTime();
  }
  
  $naissance->sejour_maman_id = $sejour_id;
  $naissance->operation_id = $operation_id;
  
  if (!$anonmymous) {
    $patient->nom = $parturiente->nom;
  }
}

$praticien = new CMediusers;
$praticiens = $praticien->loadProfessionnelDeSante(PERM_EDIT);

$smarty = new CSmartyDP;

$smarty->assign("naissance"  , $naissance);
$smarty->assign("patient"    , $patient);
$smarty->assign("constantes" , $constantes);
$smarty->assign("parturiente", $parturiente);
$smarty->assign("provisoire" , $provisoire);
$smarty->assign("sejour_id"  , $sejour_id);
$smarty->assign("callback"   , $callback);
$smarty->assign("sejour"     , $sejour);
$smarty->assign("praticiens" , $praticiens);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("list_constantes", CConstantesMedicales::$list_constantes);

$smarty->display("inc_edit_naissance.tpl");
?>