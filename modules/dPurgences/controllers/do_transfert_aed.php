<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

function viewMsg($msg, $action, $txt = ""){
  global $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    CAppUI::setMsg("$action: $msg", UI_MSG_ERROR );
    CAppUI::redirect("m=$m&tab=$tab");
    return;
  }
  CAppUI::setMsg("$action $txt", UI_MSG_OK );
}

// Récupération du rpu
$rpu_id = CValue::post("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

$sejour_rpu =& $rpu->_ref_sejour;

// Creation du nouveau sejour et pre-remplissage des champs
if (!CAppUI::conf("dPurgences create_sejour_hospit")) {
  $sejour = new CSejour();
  $sejour->patient_id   = $sejour_rpu->patient_id;
  $sejour->praticien_id = $sejour_rpu->praticien_id;
  $sejour->group_id     = $sejour_rpu->group_id;
  $sejour->entree_prevue = mbDateTime();
  $sejour->sortie_prevue = mbDateTime("+ 1 day");
  $sejour->entree_reelle = $sejour_rpu->entree;
  $sejour->chambre_seule = "0";
  $sejour->type = "comp";
  $sejour->DP = $sejour_rpu->DP;
  $sejour->DR = $sejour_rpu->DR;
  $sejour->rques  = "";
  $sejour->_en_mutation = $sejour_rpu->_id;
  
  $sejour->service_id  = $sejour_rpu->service_id;
  
  if ($rpu->diag_infirmier){ 
    $sejour->rques .= "Diagnostic infirmier: $rpu->diag_infirmier\n";
  }
  
  if ($rpu->motif){
    $sejour->rques .= "Motif de recours aux urgences: $rpu->motif";
  }
  
  $sejour->updateDBFields();
  $msg = $sejour->store();
  viewMsg($msg, "CSejour-title-create");
  
  $rpu->_ref_sejour->loadRefsConsultations();
  foreach ($rpu->_ref_sejour->_ref_consultations as $_consult) {
    $_consult->sejour_id = $sejour->_id;      
    $msg = $_consult->store();
    viewMsg($msg, "CConsultation-title-modify");
  }
    
  // Sauvegarde du sejour
  $sejour_rpu->sortie_reelle = mbDateTime();
  $sejour_rpu->mode_sortie = "mutation";
  $sejour_rpu->etablissement_transfert_id = "";
  $msg = $sejour_rpu->store();
  viewMsg($msg, "CSejour-title-close", "(Urgences)");
  
  // Chargement des prescriptions liées au RPU
  $rpu->_ref_sejour->loadRefsPrescriptions();
  foreach($rpu->_ref_sejour->_ref_prescriptions as $_prescription){
    if($_prescription->_id){
      $_prescription->object_id = $sejour->_id;
      $msg = $_prescription->store();
      viewMsg($msg, "CPrescription-msg-modify");  
    }
  }
  
  // Transfert des transmissions et observations
  $rpu->_ref_sejour->loadBackRefs("observations");
  foreach($rpu->_ref_sejour->_back["observations"] as $_obs){
  	$_obs->sejour_id = $sejour->_id;
    $msg = $_obs->store();
    viewMsg($msg, "$_obs->_class_name-msg-modify"); 
  }
	
	$rpu->_ref_sejour->loadBackRefs("transmissions");
  foreach($rpu->_ref_sejour->_back["transmissions"] as $_trans){
    $_trans->sejour_id = $sejour->_id;
    $msg = $_trans->store();
    viewMsg($msg, "$_trans->_class_name-msg-modify");  
  }
} 
// Pas de création d'un nouveau séjour lors d'une hospitalisation mais d'un changement du type d'admission 
else {
  $sejour = $sejour_rpu;
  $sejour->type = "comp";
  $sejour->_en_mutation = $sejour_rpu->_id;
  $msg = $sejour->store();
  viewMsg($msg, "CSejour-title-modify");
}

// Sauvegarde du RPU
$rpu->orientation = "HO";
$rpu->mutation_sejour_id = $sejour->_id;
$rpu->sortie_autorisee = true;
$rpu->gemsa = "4";
$msg = $rpu->store();
viewMsg($msg, "CRPU-title-close");

CAppUI::redirect("m=dPplanningOp&tab=vw_edit_sejour&sejour_id=$sejour->_id");

?>