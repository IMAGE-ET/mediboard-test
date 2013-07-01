<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$group = CGroups::loadCurrent();

$service_id = CValue::getOrSession("service_id");

if($service_id == "NP"){
  $service_id = "";
}

$cond = array();

// Chargement du service
$service = new CService();
$service->load($service_id);

// Si le service en session n'est pas dans l'etablissement courant
if(CGroups::loadCurrent()->_id != $service->group_id){
  $service_id = "";
  $service = new CService();
}

$date = CValue::getOrSession("debut");
$prescription_id = CValue::get("prescription_id");

// Chargement des configs de services
if (!$service_id) {
  $service_id = "none";
}

$configs = CConfigService::getAllFor($service_id);

// Si la date actuelle est inférieure a l'heure affichée sur le plan de soins, on affiche le plan de soins de la veille (cas de la nuit)
if (!$date) {
  //$datetime_limit = CMbDT::dateTime($configs["Poste 1"].":00:00");
  //if(CMbDT::dateTime() < $datetime_limit){
  //  $date = CMbDT::date("- 1 DAY");
  //} else {
    $date = CMbDT::date();
  //}
}

$filter_line = new CPrescriptionLineMedicament();
$filter_line->debut = $date;

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// Initialisations
$patients   = array();
$alertes    = array();
$perfs      = array();
$new        = array();
$urgences   = array();
$lines      = array();
$pancarte   = array();
$lits       = array();
$list_lines = array();
$nb_adm     = array();

$prescriptions = array();
$prescription = new CPrescription();

if ($prescription_id) {
  $prescription->load($prescription_id);
  $prescriptions[$prescription->_id] = $prescription;
}
else {
  // Chargement des prescriptions qui sont dans le service selectionné
  $ljoin = array();
  $ljoin["sejour"]      = "prescription.object_id = sejour.sejour_id";
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["lit"]         = "lit.lit_id = affectation.lit_id";
  $ljoin["chambre"]     = "chambre.chambre_id = lit.chambre_id";
  $ljoin["service"]     = "service.service_id = chambre.service_id";
  $where = array();
  $where["prescription.object_class"] = " = 'CSejour'";
  $where["prescription.type"]         = " = 'sejour'";
  $where["service.service_id"]        = " = '$service_id'";
  $where["affectation.entree"]      = " < '$date 23:59:59'";
  $where["affectation.sortie"]      = " > '$date 00:00:00'";  
  $prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
}

// Recuperation de l'heure courante
$time = CMbDT::format(null, "%H");

$tabHours = CAdministration::getTimingPlanSoins($date, $configs);
$nb_decalage = $configs["Nombre postes avant"];
$planif_manuelle = CAppUI::conf("dPprescription CPrescription planif_manuelle", $group->_guid);
$composition_dossier = array();
$bornes_composition_dossier = array();
$count_composition_dossier = array();

$date_min = "";
$date_max = "";
foreach ($tabHours as $_key_date => $_period_date) {
  foreach ($_period_date as $_key_periode => $_period_dates) {
    $count_composition_dossier[$_key_date][$_key_periode] = $planif_manuelle ? 3 : 2;
    $first_date = reset(array_keys($_period_dates));
    $first_time = reset(reset($_period_dates));
    $last_date = end(array_keys($_period_dates));
    $last_time = end(end($_period_dates));
    
    $composition_dossier[] = "$_key_date-$_key_periode";

    $bornes_composition_dossier["$_key_date-$_key_periode"]["min"] = "$first_date $first_time:00:00";
    $bornes_composition_dossier["$_key_date-$_key_periode"]["max"] = "$last_date $last_time:00:00";

    foreach ($_period_dates as $_key_real_date => $_period_hours) {
      foreach ($_period_hours as $_key_hour => $_hour) {
        if (!$date_min) {
          $date_min = "$_key_real_date $_key_hour";
        }
        $date_max = "$_key_real_date $_key_hour";
      }
      $count_composition_dossier[$_key_date][$_key_periode] += count($_period_hours);
    }
  }
}

$date_max = CMbDT::dateTime("+ 1 HOUR", $date_max);

foreach ($prescriptions as $_prescription) {
  $_prescription->calculAllPlanifSysteme();
  
  $_prescription->loadRefPatient();
  $patients[$_prescription->_ref_patient->_id] = $_prescription->_ref_patient;
  
  // Stockage de l'affectation courante dans _ref_curr_affectation du sejour
  $_prescription->_ref_object->_ref_curr_affectation = $_prescription->_ref_object->getCurrAffectation($date);
  $_prescription->_ref_object->_ref_curr_affectation->loadRefLit()->loadCompleteView();
  $_prescription->_ref_object->_ref_curr_affectation->_view = $_prescription->_ref_object->_ref_curr_affectation->_ref_lit->_view;
  
  $lits[$_prescription->_ref_object->_ref_curr_affectation->_ref_lit->_view."-".$_prescription->_id] = $_prescription->_id;
  $_prescription->loadRefPraticien();
  $_prescription->_ref_praticien->loadRefFunction();
  $_prescription->_ref_patient->loadRefPhotoIdentite();
  
  // Chargement des planifications systemes
  $planif = new CPlanificationSysteme();
  $where = array();
  $where["sejour_id"] = " = '$_prescription->object_id'";
  $where["dateTime"] = " BETWEEN '$date_min' AND '$date_max'";
  $planifs_systeme = $planif->loadList($where);
  
  // Parcours et stockage des planifications systeme dans la pancarte
  foreach ($planifs_systeme as $_planif) {
    // Chargement et stockage de la ligne
    $_planif->loadTargetObject();
    $_date = CMbDT::date($_planif->dateTime);
    
    if($_planif->_ref_object instanceof CPrescriptionLineMedicament || $_planif->_ref_object instanceof CPrescriptionLineElement){
      // Chargement de la prise
      $_planif->loadRefPrise();
      if($_planif->_ref_object instanceof CPrescriptionLineMedicament){
        $type = $_planif->_ref_object->_is_injectable ? "inj" : "med";
      }
      if($_planif->_ref_object instanceof CPrescriptionLineElement){
        $type = $_planif->_ref_object->_ref_element_prescription->_ref_category_prescription->chapitre;
      }
      $list_lines[$type][$_planif->_ref_object->_id] = $_planif->_ref_object;
      
      $qte_adm = $_planif->_ref_prise->_quantite_administrable ? $_planif->_ref_prise->_quantite_administrable : 1; 
      
      $time = CMbDT::transform($_planif->dateTime,null,"%H").":00:00";
      
      if(!isset($pancarte[$_prescription->_id]["$_date $time"][$type][$_planif->object_id]["prevue"])){
        $pancarte[$_prescription->_id]["$_date $time"][$type][$_planif->object_id]["prevue"] = 0;
      }
      $pancarte[$_prescription->_id]["$_date $time"][$type][$_planif->object_id]["prevue"] += $qte_adm;      

      if($_planif->_ref_object->_recent_modification){
        $new[$_prescription->_id]["$_date $time"] = 1;
        $pancarte[$_prescription->_id]["$_date $time"][$type][$_planif->object_id]["new"] = 1;
      }  
      
      if(!isset($cond[$_prescription->_id]["$_date $time"][$type])){
        $cond[$_prescription->_id]["$_date $time"][$type] = true;
      }
      if(!$_planif->_ref_object->conditionnel || $_planif->_ref_object->condition_active){
        $cond[$_prescription->_id]["$_date $time"][$type] = false;
      }
      
      $urg = false;
      // Creation du tableau d'urgences
      if(@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")){
        if($_planif->_ref_object->_urgence){
          $urg = true;
        }
      } else {
        if(is_array($_planif->_ref_object->_dates_urgences) && array_key_exists($_date, $_planif->_ref_object->_dates_urgences) &&
        in_array("$_date $time",  $_planif->_ref_object->_dates_urgences[$_date])){
         $urg = true;
        }  
      }
      
      if($urg){
         $urgences[$_prescription->_id]["$_date $time"] = 1;
         $pancarte[$_prescription->_id]["$_date $time"][$type][$_planif->object_id]["urgence"] = 1;
      }
    }
    
    if($_planif->_ref_object instanceof CPrescriptionLineMixItem){
      $type_line = $_planif->_ref_object->_ref_prescription_line_mix->type_line; 
      
      if($type_line == "oxygene"){
        continue;
      }
      
      if($_planif->_ref_object->_ref_prescription_line_mix->_continuite == "discontinue"){
        $planification = new CAdministration();
        $where = array();
        $where["object_class"] = " = 'CPrescriptionLineMixItem'";
        $where["object_id"] = " = '$_planif->object_id'";
        
        $_line_mix_datetime = CMbDT::format($_planif->dateTime, "%Y-%m-%d %H:00:00");
        
        $where[] = "original_dateTime = '$_line_mix_datetime'";
        $where["planification"] = " = '1'";
        $count_planif = $planification->countList($where);

        if($count_planif){
          continue;
        }  
      } elseif (CAppUI::conf("dPprescription CPrescription planif_manuelle", CGroups::loadCurrent()->_guid)) {
        continue;
      }
      
      if(!isset($cond[$_prescription->_id]["$_date $time"][$type_line])){
        $cond[$_prescription->_id]["$_date $time"][$type_line] = true;
      }
      if(!$_planif->_ref_object->_ref_prescription_line_mix->conditionnel || $_planif->_ref_object->_ref_prescription_line_mix->condition_active){
        $cond[$_prescription->_id]["$_date $time"][$type_line] = false;
      }
      
      $_planif->_ref_object->updateQuantiteAdministration();
      $list_lines[$type_line][$_planif->_ref_object->_ref_prescription_line_mix->_id] = $_planif->_ref_object->_ref_prescription_line_mix;
      $list_lines["perf_line"][$_planif->_ref_object->_id] = $_planif->_ref_object; 
      $time = CMbDT::transform($_planif->dateTime,null,"%H").":00:00";
      $_date = CMbDT::date($_planif->dateTime);
      if(!isset($pancarte[$_prescription->_id]["$_date $time"][$type_line][$_planif->_ref_object->prescription_line_mix_id][$_planif->object_id]["prevue"])){
        $pancarte[$_prescription->_id]["$_date $time"][$type_line][$_planif->_ref_object->prescription_line_mix_id][$_planif->object_id]["prevue"] = 0;
      }
      $pancarte[$_prescription->_id]["$_date $time"][$type_line][$_planif->_ref_object->prescription_line_mix_id][$_planif->object_id]["prevue"] += $_planif->_ref_object->_quantite_administration;

      if($_planif->_ref_object->_ref_prescription_line_mix->_recent_modification){
        $new[$_prescription->_id]["$_date $time"] = 1;
        $pancarte[$_prescription->_id]["$_date $time"][$type_line][$_planif->_ref_object->prescription_line_mix_id][$_planif->object_id]["new"] = 1;
      }
    }
  }
  
  // Chargement des administrations
  $administration = new CAdministration();
  $ljoin = array();
  $ljoin["prescription_line_medicament"] = "(prescription_line_medicament.prescription_line_medicament_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineMedicament')";
                                             
  $ljoin["prescription_line_element"] = "(prescription_line_element.prescription_line_element_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineElement')";
                                             
  $ljoin["prescription_line_mix_item"] = "(prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineMixItem')";                    
                                             
  $ljoin["prescription_line_mix"] = "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";           
                                                                                        
  $ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                            (prescription_line_element.prescription_id = prescription.prescription_id) OR
                            (prescription_line_mix.prescription_id = prescription.prescription_id)";
  $where = array();
  $where["prescription.prescription_id"] = " = '$_prescription->_id'";
  $where["administration.dateTime"] = " BETWEEN '$date_min' AND '$date_max'";
  $administrations = $administration->loadList($where, null, null, null, $ljoin);
  
  /*
  // Chargement des administrations
  $administration = new CAdministration();
  $administrations = array();
  
  $where = array();
  $where["prescription.prescription_id"] = " = '$_prescription->_id'";
  $where["administration.dateTime"] = " BETWEEN '$date_min' AND '$date_max'";
  
  // CPrescriptionLineMedicament
  $ljoin = array(
    "prescription_line_medicament" => "(prescription_line_medicament.prescription_line_medicament_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineMedicament')",
    "prescription"                 => "prescription_line_medicament.prescription_id = prescription.prescription_id",
  );
  $administrations = array_merge($administrations, $administration->loadList($where, null, null, null, $ljoin));
  
  // CPrescriptionLineElement
  $ljoin = array(
    "prescription_line_element"    => "(prescription_line_element.prescription_line_element_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineElement')",
    "prescription"                 => "prescription_line_element.prescription_id = prescription.prescription_id",
  );
  $administrations = array_merge($administrations, $administration->loadList($where, null, null, null, $ljoin));
  
  // CPrescriptionLineMixItem
  $ljoin = array(
    "prescription_line_mix_item"   => "(prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineMixItem')",
    "prescription_line_mix"        => "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id",
    "prescription"                 => "prescription_line_mix.prescription_id = prescription.prescription_id",
  );
  $administrations = array_merge($administrations, $administration->loadList($where, null, null, null, $ljoin));
  */
  foreach ($administrations as $_administration) {
    $time = CMbDT::transform($_administration->dateTime,null,"%H").":00:00";
    $_date = CMbDT::date($_administration->dateTime);
      
    $type_adm = $_administration->planification ? "prevue" : "adm";
      
    $_administration->loadTargetObject();
    
    if ($_administration->_ref_object instanceof CPrescriptionLineMedicament || $_administration->_ref_object instanceof CPrescriptionLineElement) {
      if ($_administration->_ref_object instanceof CPrescriptionLineMedicament) {
        $type = $_administration->_ref_object->_is_injectable ? "inj" : "med";
      }
      if ($_administration->_ref_object instanceof CPrescriptionLineElement) {
        $type = $_administration->_ref_object->_ref_element_prescription->_ref_category_prescription->chapitre;
      }    
      $_administration->_ref_object->_unite_administration = $_administration->unite_prise;
      $list_lines[$type][$_administration->_ref_object->_id] = $_administration->_ref_object;
      
      if (!isset($pancarte[$_prescription->_id]["$_date $time"][$type][$_administration->object_id][$type_adm])) {
        $pancarte[$_prescription->_id]["$_date $time"][$type][$_administration->object_id][$type_adm] = 0;
      }
      $pancarte[$_prescription->_id]["$_date $time"][$type][$_administration->object_id][$type_adm] += $_administration->quantite;
      
      if ($_administration->_ref_object->_recent_modification) {
        $new[$_prescription->_id]["$_date $time"] = 1;
        $pancarte[$_prescription->_id]["$_date $time"][$type][$_administration->object_id]["new"] = 1;
      }

      // Suppression d'une planification systeme replanifiée
      if ($type_adm == "prevue") {
        if ($_administration->original_dateTime) {
          $original_time = CMbDT::transform($_administration->original_dateTime,null,"%H").":00:00";
          $original_date = CMbDT::date($_administration->original_dateTime);
          
          if (isset( $pancarte[$_prescription->_id]["$original_date $original_time"][$type][$_administration->object_id][$type_adm])) {
            $pancarte[$_prescription->_id]["$original_date $original_time"][$type][$_administration->object_id][$type_adm] -= $_administration->quantite;
            $values =& $pancarte[$_prescription->_id]["$original_date $original_time"][$type][$_administration->object_id];
            if ($values["prevue"] == 0 && !@$values["adm"]) {
              unset($_administration->object_id, $pancarte[$_prescription->_id]["$original_date $original_time"][$type]);
            }
          }
        }
      }
    }
    
    if ($_administration->_ref_object instanceof CPrescriptionLineMixItem) {
      $type_line = $_administration->_ref_object->_ref_prescription_line_mix->type_line;

      if ($type_line == "oxygene") {
        continue;
      }
      
      $prescription_line_mix_item = $_administration->_ref_object;
      $time = CMbDT::transform($_administration->dateTime,null,"%H").":00:00";
      $_date = CMbDT::date($_administration->dateTime);
      
      $list_lines[$type_line][$_administration->_ref_object->_ref_prescription_line_mix->_id] = $_administration->_ref_object->_ref_prescription_line_mix;
      $list_lines["perf_line"][$_administration->_ref_object->_id] = $_administration->_ref_object; 
      
      if (!isset($pancarte[$_prescription->_id]["$_date $time"][$type_line][$prescription_line_mix_item->prescription_line_mix_id][$_administration->object_id][$type_adm])) {
        $pancarte[$_prescription->_id]["$_date $time"][$type_line][$prescription_line_mix_item->prescription_line_mix_id][$_administration->object_id][$type_adm] = 0;
      }
      $pancarte[$_prescription->_id]["$_date $time"][$type_line][$prescription_line_mix_item->prescription_line_mix_id][$_administration->object_id][$type_adm] += $_administration->quantite;
    }
  }                                                                                            


  foreach ($pancarte as $_prescription_id => $pancarte_by_prescription) {
    foreach ($pancarte_by_prescription as $_dateTime => $prescription_by_datetime) {
      foreach ($prescription_by_datetime as $_type => $presc_by_type) {
        if ($_type != "perfusion" && $_type != 'aerosol') {
          foreach ($presc_by_type as $prescription_by_object) {
            
            if (isset($prescription_by_object["adm"])) {
              @$nb_adm[$_prescription_id][$_dateTime][$_type]++;
            }
            
            if (!isset($prescription_by_object["adm"])) {
              $prescription_by_object["adm"] = 0;
            }
            if (!isset($prescription_by_object["prevue"])) {
              $prescription_by_object["prevue"] = 0;
            }
            if ($prescription_by_object["adm"] != $prescription_by_object["prevue"]) {
              $alertes[$_prescription_id][$_dateTime][$_type] = 1;
            }
          }
        }
        else {
          foreach ($presc_by_type as $prescription_by_object) {
            foreach ($prescription_by_object as $_prescription_by_object) {
              
              if (isset($_prescription_by_object["adm"])) {
                @$nb_adm[$_prescription_id][$_dateTime][$_type]++;
              }
            
              if (!isset($_prescription_by_object["adm"])) {
                $_prescription_by_object["adm"] = 0;
              }
              if (!isset($_prescription_by_object["prevue"])) {
                $_prescription_by_object["prevue"] = 0;
              }
              if ($_prescription_by_object["adm"] != $_prescription_by_object["prevue"]) {
                $alertes[$_prescription_id][$_dateTime][$_type] = 1;
              }  
            }
          }
        }
      }
    }
  }
}

// Classement par lit
ksort($lits);
$_prescriptions = array();
foreach ($lits as $_prescription_id) {
  $_prescriptions[$_prescription_id] = $prescriptions[$_prescription_id];
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("pancarte", $pancarte);
$smarty->assign("list_lines", $list_lines);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("count_composition_dossier", $count_composition_dossier);
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);
$smarty->assign("prescriptions", $_prescriptions);
$smarty->assign("date"     , $date);
$smarty->assign("date_min", $date_min);
$smarty->assign("service", $service);
$smarty->assign("patients", $patients);
$smarty->assign("alertes", $alertes);
$smarty->assign("configs", $configs);
$smarty->assign("nb_adm", $nb_adm);
$smarty->assign("composition_dossier", $composition_dossier);
$smarty->assign("bornes_composition_dossier", $bornes_composition_dossier);
$smarty->assign("nb_decalage", abs($nb_decalage));
$smarty->assign("manual_planif", $planif_manuelle);
$smarty->assign("new", $new);
$smarty->assign("urgences", $urgences);
$smarty->assign("filter_line", $filter_line);
$smarty->assign("cond", $cond);
if ($prescription_id) {
  $smarty->assign("_prescription_id", $prescription->_id);
  $smarty->assign("_prescription", $prescription);
  $smarty->assign("nodebug", true);
  $smarty->assign("images", CPrescription::$images);
  $smarty->display('inc_vw_line_pancarte_service.tpl');
}
else {
  $smarty->display('vw_pancarte_service.tpl');
}