<?php /* $Id: httpreq_get_last_refs.php 136 2006-06-13 22:47:54Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 136 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

$patient_id = mbGetValueFromGet("patient_id", 0);
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();
foreach($patient->_ref_sejours as $key => $sejour) {
  $patient->_ref_sejours[$key]->loadRefsFwd();
}

if ($canRead) {
  // Création du template
  require_once( $AppUI->getSystemClass ('smartydp' ) );
  $smarty = new CSmartyDP;

  $smarty->assign('sejours', $patient->_ref_sejours);

  $smarty->display('inc_select_sejours.tpl');
}