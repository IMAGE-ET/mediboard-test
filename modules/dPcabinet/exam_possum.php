<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

//$can->needsEdit();

$consultation_id = CValue::getOrSession("consultation_id");

$where = array("consultation_id" => "= '$consultation_id'");
$exam_possum = new CExamPossum;
$exam_possum->loadObject($where);

if (!$exam_possum->_id) {
  $exam_possum->consultation_id = $consultation_id;
  $exam_possum->updateFormFields();
}
$exam_possum->loadRefsFwd();

// Pré-remplissage de certaines valeurs
$consultation =& $exam_possum->_ref_consult;
$consultation->loadRefsFwd();
$consultation->loadRefConsultAnesth();
$consultation->_ref_consult_anesth->loadRefsFwd();

$patient       =& $consultation->_ref_patient;
$consultAnesth =& $consultation->_ref_consult_anesth;
$const_med     =  $patient->_ref_constantes_medicales;

$patient->evalAge($consultation->_date);
if(!$exam_possum->age && $patient->_age != "??"){
  if($patient->_age >= 71) {      $exam_possum->age = "sup71";
  }elseif($patient->_age >= 61){  $exam_possum->age = "61";
  }else{                          $exam_possum->age = "inf60";
  }
}
if(!$exam_possum->hb && $consultAnesth->hb){
  if($consultAnesth->hb <= 9.9){                                        $exam_possum->hb = "inf9.9";
  }elseif($consultAnesth->hb >= 10   && $consultAnesth->hb <= 11.4 ){  $exam_possum->hb = "10";
  }elseif($consultAnesth->hb >= 11.5 && $consultAnesth->hb <= 12.9 ){  $exam_possum->hb = "11.5";
  }elseif($consultAnesth->hb >= 13   && $consultAnesth->hb <= 16 ){    $exam_possum->hb = "13";
  }elseif($consultAnesth->hb >= 16.1 && $consultAnesth->hb <= 17 ){    $exam_possum->hb = "16.1";
  }elseif($consultAnesth->hb >= 17.1 && $consultAnesth->hb <= 18 ){    $exam_possum->hb = "17.1";
  }elseif($consultAnesth->hb >= 18.1){                                 $exam_possum->hb = "sup18.1";
  }
}

if(!$exam_possum->pression_arterielle && $const_med->ta){
  $tasys = $const_med->_ta_systole * 10;
  if($tasys <= 89){                        $exam_possum->pression_arterielle = "inf89";
  }elseif($tasys >= 90  && $tasys<= 99){  $exam_possum->pression_arterielle = "90";
  }elseif($tasys >= 100 && $tasys<= 109){ $exam_possum->pression_arterielle = "100";
  }elseif($tasys >= 110 && $tasys<= 130){ $exam_possum->pression_arterielle = "110";
  }elseif($tasys >= 131 && $tasys<= 170){ $exam_possum->pression_arterielle = "131";
  }elseif($tasys >= 171){                 $exam_possum->pression_arterielle = "sup171";
  }
}
if(!$exam_possum->kaliemie && $consultAnesth->k){
  if($consultAnesth->k <= 2.8 ){                                  $exam_possum->kaliemie = "inf2.8";
  }elseif($consultAnesth->k >= 2.9 && $consultAnesth->k <= 3.1){ $exam_possum->kaliemie = "2.9";
  }elseif($consultAnesth->k >= 3.2 && $consultAnesth->k <= 3.4){ $exam_possum->kaliemie = "3.2";
  }elseif($consultAnesth->k >= 3.5 && $consultAnesth->k <= 5.0){ $exam_possum->kaliemie = "3.5";
  }elseif($consultAnesth->k >= 5.1 && $consultAnesth->k <= 5.3){ $exam_possum->kaliemie = "5.1";
  }elseif($consultAnesth->k >= 5.4 && $consultAnesth->k <= 5.9){ $exam_possum->kaliemie = "5.4";
  }elseif($consultAnesth->k >= 6.0){                             $exam_possum->kaliemie = "sup6.0";
  }
}

if(!$exam_possum->natremie && $consultAnesth->na){
  if($consultAnesth->na <= 125 ){                                   $exam_possum->natremie = "inf125";
  }elseif($consultAnesth->na >= 126 && $consultAnesth->na <= 130){ $exam_possum->natremie = "126";
  }elseif($consultAnesth->na >= 131 && $consultAnesth->na <= 135){ $exam_possum->natremie = "131";
  }elseif($consultAnesth->na >= 136){                              $exam_possum->natremie = "sup136";
  }
}

$exam_possum->updateFormFields();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("exam_possum" , $exam_possum);

$smarty->display("exam_possum.tpl");
?>