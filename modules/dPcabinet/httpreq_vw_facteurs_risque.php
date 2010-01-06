<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPcabinet
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$consultation_id = CValue::get('consultation_id');

$consultation = new CConsultation();
$consultation->load($consultation_id);


// Chargement du dossier medical du patient
$consultation->loadRefPatient();
$patient =& $consultation->_ref_patient;
$patient->loadRefDossierMedical();

// Chargement du dossier medical du sejour
$consultation->loadRefConsultAnesth();
$consultation->_ref_consult_anesth->loadRefOperation();
$sejour =& $consultation->_ref_consult_anesth->_ref_sejour;
$sejour->loadRefDossierMedical();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $patient);
$smarty->display('inc_consult_anesth/inc_vw_facteurs_risque.tpl');

?>