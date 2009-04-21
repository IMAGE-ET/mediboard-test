<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsEdit();

$date           = mbGetValueFromGetOrSession("date"           , mbDate());
$typerepas_id   = mbGetValueFromGetOrSession("typerepas_id"   , null);
$affectation_id = mbGetValueFromGetOrSession("affectation_id" , null);

$affectation = new CAffectation;
$listRepas   = new CMenu;
$typeRepas   = new CTypeRepas;
$repas       = new CRepas;

if (!$affectation->load($affectation_id) || !$typeRepas->load($typerepas_id)){
  // Pas d'affectation
  mbSetValueToSession("affectation_id", null);
  $AppUI->setMsg("Veuillez sélectionner une affectation", UI_MSG_ALERT);
  $AppUI->redirect("m=dPrepas&tab=vw_planning_repas");
}else{
  $affectation->loadRefSejour();
  $affectation->loadRefLit();
  $affectation->_ref_lit->loadCompleteView();
  $canAffectation = $affectation->canDo();
  
  if(!$canAffectation->read || !$affectation->_ref_sejour->sejour_id || $affectation->_ref_sejour->type == "ambu"){
    // Droit Interdit ou Ambulatoire
    mbSetValueToSession("affectation_id", null);
    $affectation_id = null ;
    if(!$affectation->_canRead){
      $msg = "Vous n'avez pas les droit suffisant pour cette affectation";
    }else{
      $msg = "Vous ne pouvez pas plannifier de repas pour cette affectation";
    }
    $AppUI->setMsg($msg, UI_MSG_ALERT);
    $AppUI->redirect("m=dPrepas&tab=vw_planning_repas");
  }

  // Chargement des Repas
  $listRepas = $listRepas->loadByDate($date,$typerepas_id);  
  
  // Chargement Du Repas
  $affectation->loadMenu($date);
  $repas =& $affectation->_list_repas[$date][$typerepas_id];
  
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectation"   , $affectation);
$smarty->assign("typerepas_id"  , $typerepas_id);
$smarty->assign("date"          , $date);
$smarty->assign("listRepas"     , $listRepas);
$smarty->assign("repas"         , $repas);
$smarty->assign("typeRepas"     , $typeRepas);

$smarty->display("vw_edit_repas.tpl");

?>