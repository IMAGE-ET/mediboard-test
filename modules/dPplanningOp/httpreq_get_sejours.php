<?php /* $Id: httpreq_get_last_refs.php 136 2006-06-13 22:47:54Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 136 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

$sejour_id  = mbGetValueFromGet("sejour_id", 0);
$patient_id = mbGetValueFromGet("patient_id", 0);

echo "patient : $patient_id, sejour : $sejour_id";
exit(0);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();
foreach($patient->_ref_sejours as $key => $sejour) {
  $patient->_ref_sejours[$key]->loadRefsFwd();
}

if ($canRead) {
  // Création du template
  require_once( $AppUI->getSystemClass ('smartydp' ) );
  $smarty = new CSmartyDP(1);

  $smarty->assign('sejour_id', $sejour_id);
  $smarty->assign('sejours', $patient->_ref_sejours);

  $smarty->display('inc_select_sejours.tpl');
}

?>