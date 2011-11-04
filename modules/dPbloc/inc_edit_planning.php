<?php 

$date       = CValue::getOrSession("date", mbDate());
$plageop_id = CValue::getOrSession("plageop_id");

$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id    = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);
if(!key_exists($bloc_id, $listBlocs)) {
  $bloc_id = reset($listBlocs)->_id;
}
$listSalles = array();

foreach($listBlocs as &$curr_bloc) {
  $curr_bloc->loadRefsSalles();
}

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();

$listSalles = $bloc->_ref_salles;
  
// Informations sur la plage demandée
$plagesel = new CPlageOp;
$plagesel->load($plageop_id);
if(!$plagesel->temps_inter_op) {
  $plagesel->temps_inter_op = "00:00:00";
}
if($plagesel->_id){
  $arrKeySalle = array_keys($listSalles);
  if(!in_array($plagesel->salle_id, $arrKeySalle) || $plagesel->date != $date) {
    $plageop_id = 0;
    $plagesel = new CPlageOp;
  }
}

if(!$plagesel->_id) {
  $plagesel->debut = CPlageOp::$hours_start.":00:00";
  $plagesel->fin   = CPlageOp::$hours_start.":00:00";
}

// Liste des Specialités
$function = new CFunctions;
$specs = $function->loadSpecialites(PERM_READ, 1);

// Liste des Anesthésistes
$mediuser = new CMediusers;
$anesths = $mediuser->loadAnesthesistes();
foreach($anesths as $_anesth) {
  $_anesth->loadRefFunction();
}

// Liste des praticiens
$chirs = $mediuser->loadChirurgiens();
foreach($chirs as $_chir) {
  $_chir->loadRefFunction();
}

//Création du template
$smarty = new CSmartyDP();

$smarty->assign("listBlocs"         , $listBlocs         );
$smarty->assign("bloc"              , $bloc              );
$smarty->assign("date"              , $date              );
$smarty->assign("plagesel"          , $plagesel          );
$smarty->assign("specs"             , $specs             );
$smarty->assign("anesths"           , $anesths           );
$smarty->assign("chirs"             , $chirs             );

$smarty->display("inc_edit_planning.tpl");
?>