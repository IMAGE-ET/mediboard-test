<?php /* $Id: rpu.class.php 6716 2009-07-28 06:53:12Z mytto $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6716 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m;

$can->needsRead();

$today = date("d/m/Y");

$consult_id = CValue::get("consult_id", 0);

//Création de la consultation
$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefPatient();
$consult->loadRefSejour();
$consult->loadRefPraticien();
$consult->loadRefsBack();
$consult->loadRefsDocs();
$consult->loadComplete();

$sejour = $consult->_ref_sejour;
$sejour->loadRefsConsultations();
$sejour->loadListConstantesMedicales();
$sejour->loadNumDossier();
$sejour->loadRefsTransmissions();
$patient = $consult->_ref_patient;

$patient->loadIPP();
$patient->loadRefDossierMedical();

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->countAntecedents();
$dossier_medical->loadRefPrescription();
$dossier_medical->loadRefsTraitements();

$csteByTime = array();
foreach ($sejour->_list_constantes_medicales as $_constante_medicale) {
  $csteByTime[$_constante_medicale->datetime] = array();
  foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
    $csteByTime[$_constante_medicale->datetime][$_constante] = $_constante_medicale->$_constante;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("consult", $consult);
$smarty->assign("csteByTime", $csteByTime);
$smarty->assign("today", $today  );

$smarty->display("print_consult.tpl");

?>