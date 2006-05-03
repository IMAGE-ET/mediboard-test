<?php /* $Id: httpreq_vw_list_antecedents.php,v 1.1 2006/05/02 09:01:48 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id  = mbGetValueFromGetOrSession("patient_id", 0);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsAntecedents();
$patient->loadRefsTraitements();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;


$smarty->assign('patient', $patient);

$smarty->display('inc_list_ant.tpl');

?>