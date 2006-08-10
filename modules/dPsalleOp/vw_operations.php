<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPprotocoles
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("mediusers"   , "functions"));
require_once($AppUI->getModuleClass("dPbloc"      , "salle"    ));
require_once($AppUI->getModuleClass("dPbloc"      , "plagesop" ));
require_once($AppUI->getModuleClass("dPplanningOp", "planning" ));

$salle = mbGetValueFromGetOrSession("salle");
$op    = mbGetValueFromGetOrSession("op");
$date  = mbGetValueFromGetOrSession("date", mbDate());

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();


// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadList();

// Selection des plages opératoires de la journée
$plages = new CplageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = "= '$salle'";
$order = "debut";
$plages = $plages->loadList($where, $order);
foreach($plages as $key => $value) {
  $plages[$key]->loadRefs(0);
  foreach($plages[$key]->_ref_operations as $key2 => $value) {
    if($plages[$key]->_ref_operations[$key2]->rank == 0)
      unset($plages[$key]->_ref_operations[$key2]);
    else
      $plages[$key]->_ref_operations[$key2]->loadRefsFwd();
  }
}

$urgences = new COperation;
$where = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = "= '$salle'";
$order = "chir_id";
$urgences = $urgences->loadList($where);
foreach($urgences as $keyOp => $curr_op) {
  $urgences[$keyOp]->loadRefsFwd();
  $urgences[$keyOp]->_ref_sejour->loadRefPatient();
}

// Opération selectionnée
$selOp = new COperation;
$timing = array();
if($op) {
  $selOp->load($op);
  $selOp->loadRefs();
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
  $timing["entree_bloc"]    = array();
  $timing["pose_garrot"]    = array();
  $timing["debut_op"]       = array();
  $timing["fin_op"]         = array();
  $timing["retrait_garrot"] = array();
  $timing["sortie_bloc"]    = array();
  foreach($timing as $key => $value) {
    for($i = -10; $i < 10 && $selOp->$key !== null; $i++) {
      $timing[$key][] = mbTime("+ $i minutes", $selOp->$key);
    }
  }
}

// Création du template
require_once( $AppUI->getSystemClass ("smartydp" ) );
$smarty = new CSmartyDP(1);

$smarty->debugging = false;

$smarty->assign("salle"         , $salle                   );
$smarty->assign("listSalles"    , $listSalles              );
$smarty->assign("listAnesthType", dPgetSysVal("AnesthType"));
$smarty->assign("listAnesths"   , $listAnesths             );
$smarty->assign("listChirs"     , $listChirs               );
$smarty->assign("plages"        , $plages                  );
$smarty->assign("urgences"      , $urgences                );
$smarty->assign("selOp"         , $selOp                   );
$smarty->assign("timing"        , $timing                  );
$smarty->assign("date"          , $date                    );

$smarty->display("vw_operations.tpl");

?>