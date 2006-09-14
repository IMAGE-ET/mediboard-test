<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

$deb = mbGetValueFromGetOrSession("deb", mbDate());
$fin = mbGetValueFromGetOrSession("fin", mbDate());

$vide  = mbGetValueFromGet("vide" , false);
$type  = mbGetValueFromGet("type" , 0);
$chir  = mbGetValueFromGet("chir" , 0);
$spe   = mbGetValueFromGet("spe"  , 0);
$salle = mbGetValueFromGet("salle", 0);
$CCAM  = mbGetValueFromGet("CCAM" , "");

//On sort les plages opératoires
//  Chir - Salle - Horaires

$plagesop = new CPlageOp;

$where = array();
$where["date"] =  "BETWEEN '$deb' AND '$fin'";

$order = array();
$order[] = "date";
$order[] = "salle_id";
$order[] = "debut";

// En fonction du chirurgien
if ($chir) {
  $where["chir_id"] = "= '$chir'";
}

// @todo : rajouter en fonction de l'anesthésiste

// En fonction du cabinet
if ($spe) {
  $sql = "SELECT user_id" .
  		"\nFROM users_mediboard" .
  		"\nWHERE function_id = '$spe'";
  $listChirs = db_loadlist($sql);
  $inSpe = array();
  foreach($listChirs as $key =>$value)
    $inSpe[] = "'".$value["user_id"]."'";
  $where["chir_id"] = "IN(".implode(", ", $inSpe).")";
}

// En fonction de la salle
if ($salle) {
  $where["salle_id"] = "= '$salle'";
}

$plagesop = $plagesop->loadList($where, $order);

// Operations de chaque plage
foreach($plagesop as $keyPlage => $valuePlage) {
  $plage =& $plagesop[$keyPlage];
  $plage->loadRefsFwd();
  
  $listOp = new COperation;
  $where = array();
  $where["plageop_id"] = "= '".$valuePlage->plageop_id."'";
  switch ($type) {
    case "1" : $where["rank"] = "!= '0'"; break;
    case "2" : $where["rank"] = "= '0'"; break;
  }
  
  if ($CCAM) {
    $where["codes_ccam"] = "LIKE '%$CCAM%'";
  }
  
  $order = "operations.rank";
  $listOp = $listOp->loadList($where, $order);
  if ((sizeof($listOp) == 0) && ($vide == "false"))
    unset($plagesop[$key]);
  else {
    foreach($listOp as $keyOp => $currOp) {
      $operation =& $listOp[$keyOp];
      $operation->loadRefsFwd();
      $operation->_ref_sejour->loadRefsFwd();
      $operation->_ref_sejour->loadRefsAffectations();
      $affectation =& $operation->_ref_sejour->_ref_first_affectation;
      if ($affectation->affectation_id) {
        $affectation->loadRefsFwd();
        $affectation->_ref_lit->loadCompleteView();
      }
    }
    $plage->_ref_operations = $listOp;
  }
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("deb"     , $deb);
$smarty->assign("fin"     , $fin);
$smarty->assign("plagesop", $plagesop);

$smarty->display("view_planning.tpl");

?>