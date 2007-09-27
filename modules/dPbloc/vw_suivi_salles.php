<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc 
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsEdit();

$date_suivi  = mbGetValueFromGetOrSession("date_suivi", mbDate());


// Chargement des salles
$salle = new CSalle;
$where = array("group_id"=>"= '$g'");
$order = "'nom'";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);

// Chargement des Anesthésistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$listInfosSalles = array();
foreach($listSalles as $keySalle=>$currSalle){
  $listInfosSalles[$keySalle] = array();
  $salle =& $listInfosSalles[$keySalle];
  
  $plages = new CPlageOp;
  $where = array();
  $where["date"] = "= '$date_suivi'";
  $where["salle_id"] = "= '$keySalle'";
  $order = "debut";
  $plages = $plages->loadList($where, $order);
  foreach($plages as $key => $value) {
    $plages[$key]->loadRefs(0);
    $plages[$key]->_unordered_operations = array();
    foreach($plages[$key]->_ref_operations as $key2 => $value) {
      $plages[$key]->_ref_operations[$key2]->loadRefSejour();
      $plages[$key]->_ref_operations[$key2]->_ref_sejour->loadRefPatient();
      $plages[$key]->_ref_operations[$key2]->loadRefsCodesCCAM();
      if($plages[$key]->_ref_operations[$key2]->rank == 0) {
        $plages[$key]->_unordered_operations[$key2] = $plages[$key]->_ref_operations[$key2];
        unset($plages[$key]->_ref_operations[$key2]);
      }
    }
  }
  $salle["plages"] = $plages;
  
  $urgences = new COperation;
  $where = array();
  $where["date"]     = "= '$date_suivi'";
  $where["salle_id"] = "= '$keySalle'";
  $order = "chir_id";
  $urgences = $urgences->loadList($where);
  foreach($urgences as $keyOp => $curr_op) {
    $urgences[$keyOp]->loadRefChir();
    $urgences[$keyOp]->loadRefSejour();
    $urgences[$keyOp]->_ref_sejour->loadRefPatient();
    $urgences[$keyOp]->loadRefsCodesCCAM();
  }
  $salle["urgences"] = $urgences;
}





// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listInfosSalles", $listInfosSalles);
$smarty->assign("listSalles"     , $listSalles);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);

$smarty->display("vw_suivi_salles.tpl");
?>