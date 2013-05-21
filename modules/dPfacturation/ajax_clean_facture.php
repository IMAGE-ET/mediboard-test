<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision:$
 */

CCanDo::checkAdmin();
$resolution = CValue::get("resolution");
if ($resolution == 1) {
  $ljoin = array();
  $ljoin["facture_liaison"] = "facture_cabinet.facture_id = facture_liaison.facture_id";
  
  $where = array();
  $where["facture_liaison.object_class"]  = " = 'CConsultation'";
  $where["facture_liaison.facture_class"] = " = 'CFactureCabinet'";
  
  $group = "facture_liaison.object_id HAVING COUNT(facture_liaison.object_id)>=2";
  
  $facture = new CFactureCabinet();
  $factures = $facture->loadList($where, "praticien_id", null, $group, $ljoin);
  
  foreach ($factures as $fact) {
    $fact->loadRefPatient();
    $fact->loadRefPraticien();
    $fact->loadRefAssurance();
    $fact->loadRefsObjects();
    $fact->loadRefsReglements();
    
    $where = array();
    $where["object_id"]  = " = '".$fact->_ref_first_consult->_id."'";
    $where["object_class"]  = " = 'CConsultation'";
    $where["facture_class"] = " = 'CFactureCabinet'";
    $liaison = new CFactureLiaison();
    $liaisons = $liaison->loadList($where);
    
    $facts = array();
    foreach ($liaisons as $lien) {
      $reglement = $lien->loadRefFacture()->loadRefsReglements();
      $facts[] = $lien->_ref_facture;
    }
    if (!count($facts[0]->_ref_reglements)) {
      $facts[0]->delete();
    }
    elseif (!count($facts[1]->_ref_reglements)) {
      $facts[1]->delete();
    }
  }
}
elseif ($resolution == 2) {
  $where = array();
  $where["object_class"]  = " = 'CConsultation'";
  $where["facture_class"] = " = 'CFactureCabinet'";
  
  $group = "facture_id HAVING COUNT(facture_id) >= 2";
  
  $liaison = new CFactureLiaison();
  $liaisons = $liaison->loadList($where, null, null, $group);
  foreach ($liaisons as $lien) {
    $facture = $lien->loadRefFacture();
    $facture->loadRefPatient();
    $facture->loadRefPraticien();
    $facture->loadRefAssurance();
    $facture->loadRefsObjects();
    $facture->loadRefsReglements();
    $facture->loadRefsRelances();
    $facture->loadRefsNotes();
    if (count($facture->_ref_consults)>=2) {
      $consult = new CConsultation();
      $consult->load($facture->_ref_last_consult->_id);
      
      $newfact = new CFactureCabinet();
      $newfact->ouverture = $consult->_date;
      $newfact->cloture   = $consult->_date;
      $newfact->patient_id = $facture->patient_id;
      $newfact->praticien_id = $facture->praticien_id;
      $newfact->type_facture = $facture->type_facture;
      $newfact->du_patient = $consult->du_patient;
      $newfact->du_tiers = $consult->du_tiers;
      $newfact->store();
      
      $ligne = new CFactureLiaison();
      $ligne->facture_id    = $facture->_id;
      $ligne->facture_class = $newfact->_class;
      $ligne->object_id     = $consult->_id;
      $ligne->object_class  = 'CConsultation';
      if ($ligne->loadMatchingObject()) {
        $ligne->facture_id = $newfact->_id;
        $ligne->store();
      }
    }
  }
}

$where = array();
$where["object_class"]  = " = 'CConsultation'";
$where["facture_class"] = " = 'CFactureCabinet'";

$group = "facture_id HAVING COUNT(facture_id) >= 2";

$liaison = new CFactureLiaison();
$liaisons = $liaison->loadList($where, null, null, $group);
foreach ($liaisons as $lien) {
  $fact = $lien->loadRefFacture();
  $fact->loadRefPatient();
  $fact->loadRefPraticien();
  $fact->loadRefsObjects();
  $fact->loadRefsReglements();
}

$ljoin = array();
$ljoin["facture_liaison"] = "facture_cabinet.facture_id = facture_liaison.facture_id";
$where = array();
$where["facture_liaison.object_class"]  = " = 'CConsultation'";
$where["facture_liaison.facture_class"] = " = 'CFactureCabinet'";

$group = "facture_liaison.object_id HAVING COUNT(facture_liaison.object_id) >= 2";

$facture = new CFactureCabinet();
$factures = $facture->loadList($where, "praticien_id", null, $group, $ljoin);
foreach ($factures as $_facture) {
  $_facture->loadRefPatient();
  $_facture->_ref_patient->loadRefsCorrespondantsPatient();
  $_facture->loadRefPraticien();
  $_facture->loadRefAssurance();
  $_facture->loadRefsObjects();
  $_facture->loadRefsReglements();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"  , $factures);
$smarty->assign("liaisons"  , $liaisons);

$smarty->display("inc_configure_resolutions.tpl");
?>