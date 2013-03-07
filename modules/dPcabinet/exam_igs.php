<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

$sejour_id = CValue::get("sejour_id"); 
$exam_igs_id = CValue::get("exam_igs_id");
$date = CValue::get("date", CMbDT::dateTime());

// Chargement du séjour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du patient
$sejour->loadRefPatient();
$patient = $sejour->_ref_patient;

$exam_igs = new CExamIgs();

$last_constantes = array();

if ($exam_igs_id) {
  $exam_igs->load($exam_igs_id);
}
else {  
  // Pre-remplissage de l'age du patient
  $age_patient = $patient->_annees;
  if ($age_patient < 40) {
    $exam_igs->age = '0'; 
  }
  elseif ($age_patient <= 59) {
    $exam_igs->age = '7'; 
  }
  elseif ($age_patient <= 69) {
    $exam_igs->age = '12'; 
  }
  elseif ($age_patient <= 74) {
    $exam_igs->age = '15'; 
  }
  elseif ($age_patient <= 79) {
    $exam_igs->age = '16'; 
  }
  else {
    $exam_igs->age = '18';
  }
  
  // Pre-remplissage des constantes médicales: FC, TA, temp, diurese (l/jour)
  list($constantes_medicales, $dates) = CConstantesMedicales::getLatestFor($patient, $date);
  
  $FC = $constantes_medicales->pouls;
  if ($FC) {
    $last_constantes["FC"] = $FC;
    if ($FC < 40) {
      $exam_igs->FC = '11'; 
    }
    elseif ($FC <= 69) {
      $exam_igs->FC = '2'; 
    }
    elseif ($FC <= 119) {
      $exam_igs->FC = '0'; 
    }
    elseif ($FC <= 159) {
      $exam_igs->FC = '4'; 
    }
    else {
      $exam_igs->FC = '7';
    }
  }
  
  $_TA = explode("|", $constantes_medicales->ta);
  $TA = $_TA[0]*10;
  if ($TA) {
    $last_constantes["TA"] = $TA;
    if ($TA < 70) {
      $exam_igs->TA = '13'; 
    }
    elseif ($TA <= 99) {
      $exam_igs->TA = '5'; 
    }
    elseif ($TA <= 199) {
      $exam_igs->TA= '0'; 
    }
    else {
      $exam_igs->TA = '2';
    }
  }
  
  $glasgow = $constantes_medicales->glasgow;
  if ($glasgow) {
    $last_constantes["glasgow"] = $glasgow;
    if ($glasgow < 6) {
      $exam_igs->glasgow = '26'; 
    }
    elseif ($glasgow <= 8) {
      $exam_igs->glasgow = '13'; 
    }
    elseif ($glasgow <= 10) {
      $exam_igs->glasgow = '7'; 
    }
    elseif ($glasgow <= 13) {
      $exam_igs->glasgow = '5'; 
    }
    else {
      $exam_igs->glasgow = '0';
    }
  }
  
  $temp = $constantes_medicales->temperature;
  if ($temp) {
    $last_constantes["temperature"] = $temp;
    $exam_igs->temperature = $temp < 39 ? '0' : '3';
  }

  $diurese = $constantes_medicales->_diurese;
  if ($diurese && $diurese != " ") { // hacky
    $last_constantes["diurese"] = $diurese;
    if ($diurese < 500) {
      $exam_igs->diurese = '11';
    }
    elseif ($diurese < 1000) {
      $exam_igs->diurese = '4';
    }
    else {
      $exam_igs->diurese = '0';
    }   
  }
  
  $exam_igs->date = $date;
}

if ($exam_igs->_id && !$exam_igs->date) {
  $exam_igs->loadLastLog();
  $exam_igs->date = $exam_igs->_ref_last_log->date;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("exam_igs", $exam_igs);
$smarty->assign("last_constantes", $last_constantes);

$smarty->display('exam_igs.tpl');
