<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author Alexis Granger
 */

set_time_limit(180);

$_date_min = mbGetValueFromGetOrSession("_date_min");
$_date_max = mbGetValueFromGetOrSession("_date_max");
$chir_id = mbGetValueFromGetOrSession("chir_id");
$typeVue = mbGetValueFromGetOrSession("typeVue");
$etatReglement = mbGetValueFromGetOrSession("etatReglement");

$nbActes = array();
$montantSejour = array();
$tabSejours = array();

$praticien = new CMediusers();
$praticien->load($chir_id);

// Parcours des actes CCAM
$acte_ccam = new CActeCCAM();
$order = "execution ASC";
$where = array();
$where["executant_id"] = " = '$chir_id'";
$where["execution"] = "BETWEEN '$_date_min' AND '$_date_max'";

//$where[] = "(object_class <> 'CConsultation' OR consultation.sejour_id IS NOT NULL AND consultation.consultation_id = object_id)"; 
$actes_ccam = $acte_ccam->loadList($where, $order);

// Initialisation du tableau de codables
$codables = array(
  "COperation"    => array(), 
  "CSejour"       => array(), 
  "CConsultation" => array(),
);

// Parcours des actes ccam
foreach($actes_ccam as $key => $acte_ccam) {
  if(!array_key_exists($acte_ccam->object_id,$codables[$acte_ccam->object_class])){
    $codable = new $acte_ccam->object_class;
    $codable->load($acte_ccam->object_id);
  }
  $codables[$acte_ccam->object_class][$acte_ccam->object_id] = $codable;
  $codables[$acte_ccam->object_class][$acte_ccam->object_id]->_ref_actes_ccam[$acte_ccam->_id] = $acte_ccam;
}

// Parcours des operations
foreach($codables["COperation"] as $key => $operation){
  $operation->loadRefPlageOp();
  // si le sejour_id n'est pas présent dans le tableau de sejour, on le crée
  if(!array_key_exists($operation->sejour_id, $codables["CSejour"])){
    $sejour = new CSejour();
    $sejour->load($operation->sejour_id);
    $sejour->_ref_operations = array();
    $sejour->_ref_operations[$operation->_id] = $operation; 
    $codables["CSejour"][$operation->sejour_id] = $sejour;
  } else {
  // sinon, on rajouter directement l'operation dans le sejour
    $codables["CSejour"][$operation->sejour_id]->_ref_operations[$operation->_id] = $operation;
  }
}

// Suppression des consultations qui n'ont pas de sejour_id
foreach($codables["CConsultation"] as $key => $consultation){
  if(!$consultation->sejour_id){
    unset($codables["CConsultation"][$key]);
  }
}

// Parcours des consultations
foreach($codables["CConsultation"] as $key => $consultation){  
  if(!array_key_exists($consultation->sejour_id, $codables["CSejour"])){
    $consultation->loadRefSejour();
    $sejour = new CSejour();
    $sejour = $consultation->_ref_sejour;
    $sejour->_ref_consultations[$consultation->_id] = $consultation;
    $codables["CSejour"][$consultation->sejour_id] = $sejour;  
  } else {
    $codables["CSejour"][$consultation->sejour_id]->_ref_consultations[$consultation->_id] = $consultation;
  }
}

$sejours =& $codables["CSejour"];

// Tri par sortie reelle et chargement des patients
foreach($sejours as $key => $sejour){
  if($sejour->sortie_reelle){    
    
    $sejour->loadRefPatient();
    $tabSejours[mbDate($sejour->sortie_reelle)][$sejour->_id] = $sejour;
    
    // Calcul du nombre d'actes par sejour
    if($sejour->_ref_actes_ccam){
      foreach($sejour->_ref_actes_ccam as $acte){
        @$nbActes[$sejour->_id]++;
        @$montantSejour[$sejour->_id] += $acte->_montant_facture; 
      }
    }
    if($sejour->_ref_operations){
      foreach($sejour->_ref_operations as $operation){
        foreach($operation->_ref_actes_ccam as $acte){
          @$nbActes[$sejour->_id]++;
          @$montantSejour[$sejour->_id] += $acte->_montant_facture;
        }
      }
    }
    if($sejour->_ref_consultations){
      foreach($sejour->_ref_consultations as $consult){
        foreach($consult->_ref_actes_ccam as $acte){
          @$nbActes[$sejour->_id]++;
          @$montantSejour[$sejour->_id] += $acte->_montant_facture;
        }
      }
    }
  }
}

$totalActes = 0;
$montantTotalActes = 0;

// Calcul du nombre total d'actes
foreach($nbActes as $key => $nb_acte_sejour){
  $totalActes += $nb_acte_sejour;
}

// Calcul du montant total des actes realises
foreach($montantSejour as $key => $montant_sejour){
  $montantTotalActes += $montant_sejour;
}

// Tri par date du tableau de sejours
ksort($tabSejours);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("montantTotalActes", $montantTotalActes);
$smarty->assign("totalActes",$totalActes);
$smarty->assign("nbActes",$nbActes);
$smarty->assign("sejours", $tabSejours);
$smarty->assign("montantSejour", $montantSejour);
$smarty->assign("praticien", $praticien);
$smarty->assign("_date_min", $_date_min);
$smarty->assign("_date_max", $_date_max);
$smarty->assign("typeVue",$typeVue);

$smarty->display("vw_actes_realises.tpl");

?>