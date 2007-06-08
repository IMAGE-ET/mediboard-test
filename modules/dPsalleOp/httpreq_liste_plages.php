<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsaleOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$salle = mbGetValueFromGetOrSession("salle");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$operation_id = mbGetValueFromGetOrSession("operation_id");

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Selection des salles
$listSalles = new CSalle;
$where = array("group_id"=>"= '$g'");
$listSalles = $listSalles->loadList($where);

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = "= '$salle'";
$order = "debut";
$plages = $plages->loadList($where, $order);
foreach($plages as $key => $value) {
  $plages[$key]->loadRefs(0);
  foreach($plages[$key]->_ref_operations as $key2 => $value) {
    if($plages[$key]->_ref_operations[$key2]->rank == 0) {
      unset($plages[$key]->_ref_operations[$key2]);
    }
    else {
      $plages[$key]->_ref_operations[$key2]->loadRefSejour();
      $plages[$key]->_ref_operations[$key2]->_ref_sejour->loadRefPatient();
      $plages[$key]->_ref_operations[$key2]->loadRefsCodesCCAM();
    }
  }
}

$urgences = new COperation;
$where = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = "= '$salle'";
$order = "chir_id";
$urgences = $urgences->loadList($where);
foreach($urgences as $keyOp => $curr_op) {
  $urgences[$keyOp]->loadRefChir();
  $urgences[$keyOp]->loadRefSejour();
  $urgences[$keyOp]->_ref_sejour->loadRefPatient();
  $urgences[$keyOp]->loadRefsCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false       );
$smarty->assign("salle"         , $salle       );
$smarty->assign("listSalles"    , $listSalles  );
$smarty->assign("listAnesths"   , $listAnesths );
$smarty->assign("plages"        , $plages      );
$smarty->assign("urgences"      , $urgences    );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_plages.tpl");
?>