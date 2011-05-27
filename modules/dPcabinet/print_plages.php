<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();
$now = mbDate();

$filter = new CConsultation;
$filter->_id = CValue::get("plage_id"       , null);
$filter->_date_min = CValue::get("_date_min", "$now");
$filter->_date_max = CValue::get("_date_max", "$now");
$filter->_coordonnees = CValue::get("_coordonnees");
$filter->_plages_vides = CValue::get("_plages_vides", 1);
$filter->_non_pourvues = CValue::get("_non_pourvues", 1);

$chir = CValue::getOrSession("chir");

// On selectionne les plages
$listPlage = new CPlageconsult;
$where = array();
if($filter->_id) {
	$filter->load($filter->_id);
  $filter->loadRefsFwd();
	$where["plageconsult_id"] = "= '$filter->_id'";
} else {
  $where["date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
  
  // Liste des praticiens
  $mediusers = new CMediusers();
  if(CAppUI::pref("pratOnlyForConsult", 1)) {
    $listPrat = $mediusers->loadPraticiens(PERM_EDIT);
  } else {
    $listPrat = $mediusers->loadProfessionnelDeSante(PERM_EDIT);
  }
  $where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir);
}
$order = array();
$order[] = "date";
$order[] = "chir_id";
$order[] = "debut";
$listPlage = $listPlage->loadList($where, $order);

// Pour chaque plage on selectionne les consultations
foreach ($listPlage as $plage_id => &$plage) {
  $plage->listPlace = array();
  $plage->listBefore  = array();
  $plage->listAfter   = array();
  $listPlage[$plage_id]->loadRefs(false, 1);
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $plage->listPlace[$i]["time"] = mbTime("+ $minutes minutes", $plage->debut);
    $plage->listPlace[$i]["consultations"] = array();
  }
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    $consultation->loadRefPatient(1);
    // Chargement de la categorie
    $consultation->loadRefCategorie(1);
    $consultation->loadRefConsultAnesth();
    $consult_anesth =& $consultation->_ref_consult_anesth;
    if($consult_anesth->operation_id){
      $consult_anesth->loadRefOperation();
      $consult_anesth->_ref_operation->loadRefPraticien(true);
      $consult_anesth->_ref_operation->loadRefPlageOp(true);
      $consult_anesth->_ref_operation->loadExtCodesCCAM();
      $consult_anesth->_date_op =& $consult_anesth->_ref_operation->_ref_plageop->date;
    } 
    
    $keyPlace = mbTimeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
  
    if($keyPlace < 0) {
      $plage->listBefore[$keyPlace] =& $consultation;
    }
    
    if($consultation->heure >= $plage->fin) {
      $plage->listAfter[$keyPlace] =& $consultation;
    }
    
    for  ($i = 0;  $i < $consultation->duree; $i++) {
      if (isset($plage->listPlace[($keyPlace + $i)])) {
        $plage->listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
      }
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

$smarty->assign("coordonnees", $filter->_coordonnees);
$smarty->assign("filter"     , $filter);
$smarty->assign("listPlage"  , $listPlage);

$smarty->display("print_plages.tpl");

?>