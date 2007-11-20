<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$salle = mbGetValueFromGetOrSession("salle");
$op    = mbGetValueFromGetOrSession("op");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);


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

// Opération selectionnée
$selOp = new COperation;
$timing = array();
if($op) {
  $selOp->load($op);
  
  $selOp->loadRefs();
  
  $selOp->_ref_sejour->loadRefDiagnosticPrincipal();

  foreach($selOp->_ext_codes_ccam as $keyCode => $code) {
    $selOp->_ext_codes_ccam[$keyCode]->Load();
  }
  $selOp->loadPossibleActes();
  /* Loading des comptes-rendus
  foreach($selOp->_ext_codes_ccam as $keyCode => $code) {
    foreach($code->activites as $keyActivite => $activite) {
      foreach($activite->phases as $keyPhase => $phase) {
        if($phase->_connected_acte->acte_id) {
          
        }
      }
    }
  }*/
  $selOp->_ref_plageop->loadRefsFwd();
  // Tableau des timings
  $timing["entree_salle"]    = array();
  $timing["pose_garrot"]     = array();
  $timing["debut_op"]        = array();
  $timing["fin_op"]          = array();
  $timing["retrait_garrot"]  = array();
  $timing["sortie_salle"]    = array();
  $timing["induction_debut"] = array();
  $timing["induction_fin"]   = array();
  foreach($timing as $key => $value) {
    for($i = -10; $i < 10 && $selOp->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $selOp->$key);
    }
  }
}

$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);


// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;
$smarty->assign("op"            , $op                      );
$smarty->assign("vueReduite"    , false                    );
$smarty->assign("salle"         , $salle                   );
$smarty->assign("listSalles"    , $listSalles              );
$smarty->assign("listAnesthType", $listAnesthType          );
$smarty->assign("listAnesths"   , $listAnesths             );
$smarty->assign("listChirs"     , $listChirs               );
$smarty->assign("plages"        , $plages                  );
$smarty->assign("urgences"      , $urgences                );
$smarty->assign("selOp"         , $selOp                   );
$smarty->assign("timing"        , $timing                  );
$smarty->assign("date"          , $date                    );
$smarty->assign("modif_operation", $modif_operation        );

$smarty->display("vw_operations.tpl");

?>