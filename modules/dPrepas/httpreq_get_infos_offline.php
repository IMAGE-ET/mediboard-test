<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

$service_id = mbGetValueFromGet("service_id" , null);
$date       = mbGetValueFromGet("date"       , mbDate());

$service = null;

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération des services
$order = "group_id, nom";
$where = array();
$where["group_id"] = CSQLDataSource::prepareIn(array_keys($etablissements));
$services = new CService;
$services = $services->loadList($where, $order);

$listTypeRepas = new CTypeRepas;
$order = "debut, fin, nom";
$listTypeRepas = $listTypeRepas->loadList(null,$order);

$aAffectation  = array();
$aPatients     = array();
$aMenus        = array();
$aRepas        = array();
$planningRepas = array();
$aSejours      = array();
$aPlats        = array();

$plats = new CPlat;
$order = "nom";
$aPlats = $plats->loadList(null,$order);

if(!$service_id || !array_key_exists($service_id,$services)){
  mbSetValueToSession("service_id", null);
  $service_id = null ;
}else{
  $service =& $services[$service_id].
  $service->loadRefsBack();
  
  foreach ($service->_ref_chambres as $chambre_id => &$chambre) {
    $chambre->loadRefsBack();
    foreach ($chambre->_ref_lits as $lit_id => &$lit) {
      $lit->loadAffectations($date);
      foreach ($lit->_ref_affectations as $affectation_id => &$affectation) {
        $affectation->loadRefSejour();
        
        if(!$affectation->_ref_sejour->sejour_id || $affectation->_ref_sejour->type == "ambu"){
          unset($lit->_ref_affectations[$affectation_id]);
        }else{
          $affectation->_ref_sejour->_ref_patient =& getCachedPatient($affectation->_ref_sejour->patient_id);
          $affectation->loadMenu($date,$listTypeRepas);
          
          $affectation->_view = $chambre->_view . " - " . $lit->_view;
          $aAffectation[$affectation->_id]   = $affectation;
          $planningRepas[$affectation->_id]  = array();
          $aSejours[$affectation->sejour_id] = $affectation->_ref_sejour;
          
          $planning =& $planningRepas[$affectation->_id];
          $patient  =& $affectation->_ref_sejour->_ref_patient;
          $repas    =& $affectation->_list_repas[$date];
          
          if($patient->_id){
            $aPatients["$patient->_id"] = $patient;
          }
          
          foreach($listTypeRepas as $keyType => $valType){
            $heure_entree = mbTransformTime(null,$affectation->entree,"%H:%M");
            $heure_sortie = mbTransformTime(null,$affectation->sortie,"%H:%M");
            $date_entree  = mbTransformTime(null,$affectation->entree,"%Y-%m-%d");
            $date_sortie  = mbTransformTime(null,$affectation->sortie,"%Y-%m-%d");
            
            if(($date == $date_entree && $heure_entree > $valType->fin) ||
                ($date == $date_sortie && $valType->debut > $heure_sortie)){
              $planning[$keyType] = "null";
              continue;    
            }
            $planning[$keyType] = array("repas_id" => 0, "_tmp_repas_id" => 0);

            if($repas[$keyType]->repas_id){
              $repas[$keyType]->del  = 0;
              $repas[$keyType]->_del = 0;
              $repas[$keyType]->_tmp_repas_id = 0;
              $repas_id = $repas[$keyType]->repas_id;
              $planning[$keyType]["repas_id"] = $repas_id;
              $aRepas[$repas_id] = $repas[$keyType];
            }
          }
        }
      }
    }
  }
}

$dPrepas = array();
$dPrepas["date_synchro"]      = time();
$dPrepas["CRepas_date"]       = $date;
$dPrepas["CRepas_service_id"] = $service_id;
$dPrepas["CRepas_modif"]      = "0";
$dPrepas["etatOffline"]       = "1";

$listRepas   = new CMenu;
foreach($listTypeRepas as $keyType => $valType){
  $aMenus[$keyType] = array();
  $aMenus[$keyType] = $listRepas->loadByDate($date,$keyType);
}
// *************************************
function delNoScalar(&$aObjects){
  foreach($aObjects as $keyObject => &$object){
    if(!is_object($object)){
     continue; 
    }
    foreach (get_object_vars($object) as $keyVar => $value) {
      if((!is_scalar($value) || $value === null) && $keyVar[0] == "_"){
        unset($object->$keyVar);
      }else{
        $object->$keyVar = htmlentities($value);
      }
    }
  }
}

delNoScalar($aAffectation);
delNoScalar($aPatients);
delNoScalar($listTypeRepas);
delNoScalar($aRepas);
delNoScalar($aSejours);
delNoScalar($aPlats);
foreach($listTypeRepas as $keyType => $valType){
  delNoScalar($aMenus[$keyType]);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dPrepas"       , $dPrepas);
$smarty->assign("aPlats"        , $aPlats);
$smarty->assign("aSejours"      , $aSejours);
$smarty->assign("planningRepas" , $planningRepas);
$smarty->assign("aAffectation"  , $aAffectation);
$smarty->assign("aPatients"     , $aPatients);
$smarty->assign("listTypeRepas" , $listTypeRepas);
$smarty->assign("aMenus"        , $aMenus);
$smarty->assign("aRepas"        , $aRepas);

$smarty->display("httpreq_get_infos_offline.tpl");

?>