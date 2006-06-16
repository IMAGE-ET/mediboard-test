<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

$sejour_id  = mbGetValueFromGet("sejour_id", 0);
$patient_id = mbGetValueFromGet("patient_id", 0);

$date = mbDate()." 00:00:00";

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();
$sejours =& $patient->_ref_sejours;
foreach($sejours as $key => $curr_sejour) {
  if($sejours[$key]->sortie_prevue < $date) {
    unset($sejours[$key]);
  }
}

if ($canRead) {
  // Création du template
  require_once( $AppUI->getSystemClass ('smartydp' ) );
  $smarty = new CSmartyDP(1);

  $smarty->assign('sejour_id', $sejour_id);
  $smarty->assign('sejours', $sejours);

  $smarty->display('inc_select_sejours.tpl');
}

?>