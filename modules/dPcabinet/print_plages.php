<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();
$now = CMbDT::date();

$filter = new CConsultation;
$filter->plageconsult_id = CValue::get("plage_id", null);
$filter->_date_min       = CValue::get("_date_min", "$now");
$filter->_date_max       = CValue::get("_date_max", "$now");
$filter->_telephone      = CValue::get("_telephone");
$filter->_coordonnees    = CValue::get("_coordonnees");
$filter->_plages_vides   = CValue::get("_plages_vides", 1);
$filter->_non_pourvues   = CValue::get("_non_pourvues", 1);

$chir = CValue::getOrSession("chir");
$show_lit = false;

// On selectionne les plages
$plage = new CPlageconsult;
$where = array();

if ($filter->plageconsult_id) {
  $plage->load($filter->plageconsult_id);
  $filter->_date_min = $filter->_date_max = $plage->date;
  $filter->_ref_plageconsult = $plage;
	$where["plageconsult_id"] = "= '$filter->plageconsult_id'";
} 
else {
  $where["date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
  
  // Liste des praticiens
  $mediusers = new CMediusers();
  if (CAppUI::pref("pratOnlyForConsult", 1)) {
    $listPrat = $mediusers->loadPraticiens(PERM_EDIT);
  }
  else {
    $listPrat = $mediusers->loadProfessionnelDeSante(PERM_EDIT);
  }
  
  $where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir);
}

$order = array();
$order[] = "date";
$order[] = "chir_id";
$order[] = "debut";
$listPlage = $plage->loadList($where, $order);

// Pour chaque plage on selectionne les consultations
foreach ($listPlage as $plage_id => &$plage) {
  $plage->listPlace = array();
  $plage->listBefore  = array();
  $plage->listAfter   = array();
  $listPlage[$plage_id]->loadRefs(false, 1);
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $plage->listPlace[$i]["time"] = CMbDT::time("+ $minutes minutes", $plage->debut);
    $plage->listPlace[$i]["consultations"] = array();
  }
  
  CMbObject::massLoadFwdRef($plage->_ref_consultations, "sejour_id");
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    
    $patient = $consultation->loadRefPatient(1);
    
    if ($consultation->sejour_id) {
      $patient->_ref_curr_affectation = $consultation->loadRefSejour()->loadRefCurrAffectation(CMbDT::date($consultation->_datetime));
      $patient->_ref_curr_affectation->loadView();
      if ($patient->_ref_curr_affectation->_id) {
        $show_lit = true;
      }
    }
    
    // Chargement de la categorie
    $consultation->loadRefCategorie(1);
    $consultation->loadRefConsultAnesth();
    $consult_anesth =& $consultation->_ref_consult_anesth;
    if ($consult_anesth->operation_id) {
      $consult_anesth->loadRefOperation();
      $consult_anesth->_ref_operation->loadRefPraticien(true);
      $consult_anesth->_ref_operation->loadRefPlageOp(true);
      $consult_anesth->_ref_operation->loadExtCodesCCAM();
      $consult_anesth->_date_op =& $consult_anesth->_ref_operation->_ref_plageop->date;
    } 
    
    $keyPlace = CMbDT::timeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
    
    for ($i = 0;  $i < $consultation->duree; $i++) {
      @$plage->listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
    }
  }
}

// Suppression des plages vides
if (!$filter->_plages_vides) {
  foreach ($listPlage as $plage_id => $plage) {
    if (!count($plage->_ref_consultations)) {
      unset($listPlage[$plage_id]);
    }
  }
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"     , $filter);
$smarty->assign("listPlage"  , $listPlage);
$smarty->assign("show_lit"   , $show_lit);
$smarty->display("print_plages.tpl");

?>