<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Sébastien Fillonneau
 */

global $can, $g;
$can->needsRead();

$salle_id     = mbGetValueFromGetOrSession("salle");
$date         = mbGetValueFromGetOrSession("date", mbDate());
$operation_id = mbGetValueFromGetOrSession("operation_id");
$hide_finished = mbGetValueFromGetOrSession("hide_finished", 0);

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Selection des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

// Selection des plages opératoires de la journée
$salle = new CSalle;
if ($salle->load($salle_id)) {
  $salle->loadRefsForDay($date); 
}

if ($hide_finished == 1 && $salle->_ref_plages) {
  foreach($salle->_ref_plages as &$plage) {
    foreach($plage->_ref_operations as $key => $op){
      if ($op->sortie_salle) unset($plage->_ref_operations[$key]);
    }
    foreach($plage->_unordered_operations as $key => $op){
      if ($op->sortie_salle) unset($plage->_unordered_operations[$key]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("salle"         , $salle       );
$smarty->assign("hide_finished" , $hide_finished);
$smarty->assign("praticien_id"  , null         );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listAnesths"   , $listAnesths );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_plages.tpl");
?>