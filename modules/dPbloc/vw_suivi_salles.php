<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc 
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if (!$canEdit) {
    $AppUI->redirect("m=system&a=access_denied");
}

$date  = mbGetValueFromGetOrSession("date", mbDate());


// Chargement des salles
$listSalles = new CSalle;
$where = array("group_id"=>"= '$g'");
$listSalles = $listSalles->loadList($where);

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
  
  $plages = new CplageOp;
  $where = array();
  $where["date"] = "= '$date'";
  $where["salle_id"] = "= '$keySalle'";
  $order = "debut";
  $plages = $plages->loadList($where, $order);
  foreach($plages as $key => $value) {
    $plages[$key]->loadRefs(0);
    foreach($plages[$key]->_ref_operations as $key2 => $value) {
      if($plages[$key]->_ref_operations[$key2]->rank == 0) {
        unset($plages[$key]->_ref_operations[$key2]);
      }else {
        $plages[$key]->_ref_operations[$key2]->loadRefSejour();
        $plages[$key]->_ref_operations[$key2]->_ref_sejour->loadRefPatient();
        $plages[$key]->_ref_operations[$key2]->loadRefCCAM();
      }
    }
  }
  $salle["plages"] = $plages;
  
  $urgences = new COperation;
  $where = array();
  $where["date"]     = "= '$date'";
  $where["salle_id"] = "= '$keySalle'";
  $order = "chir_id";
  $urgences = $urgences->loadList($where);
  foreach($urgences as $keyOp => $curr_op) {
    $urgences[$keyOp]->loadRefChir();
    $urgences[$keyOp]->loadRefSejour();
    $urgences[$keyOp]->_ref_sejour->loadRefPatient();
    $urgences[$keyOp]->loadRefCCAM();
  }
  $salle["urgences"] = $urgences;
}





// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listInfosSalles", $listInfosSalles);
$smarty->assign("listSalles"     , $listSalles);
$smarty->assign("date"           , $date);

$smarty->display("vw_suivi_salles.tpl");
?>