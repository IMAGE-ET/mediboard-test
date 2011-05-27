<?php /* $Id: rpu.class.php 6716 2009-07-28 06:53:12Z mytto $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6716 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$today = date("d/m/Y");

$rpu_id = CValue::get("rpu_id", 0);

//Cr�ation du rpu
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadComplete();

$sejour = $rpu->_ref_sejour;
$sejour->loadRefsConsultations();
$sejour->loadListConstantesMedicales();
$sejour->loadSuiviMedical();
$patient = $sejour->_ref_patient;
$patient->loadRefConstantesMedicales();
$patient->loadIPP();
$patient->loadRefDossierMedical();

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->countAntecedents();
$dossier_medical->loadRefPrescription();
$dossier_medical->loadRefsTraitements();

$consult = $sejour->_ref_consult_atu;
$consult->loadRefPatient();
$consult->loadRefPraticien();
$consult->loadRefsBack();
$consult->loadRefsDocs();
foreach ($consult->_ref_actes_ccam as $_ccam) {
	$_ccam->loadRefExecutant();
}

$csteByTime = array();
$i = 0;
foreach ($sejour->_list_constantes_medicales as $_constante_medicale) {
  $csteByTime[$i][$_constante_medicale->datetime] = array();  
  foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
    if (count($csteByTime[$i][$_constante_medicale->datetime]) > 9) $i ++;
    $csteByTime[$i][$_constante_medicale->datetime][$_constante] = $_constante_medicale->$_constante;
  }
  $i++;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("rpu"    , $rpu);
$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("consult", $consult);
$smarty->assign("csteByTime", $csteByTime);
$smarty->assign("today", $today  );
$smarty->assign("params"              , CConstantesMedicales::$list_constantes);
$smarty->display("print_dossier.tpl");

?>