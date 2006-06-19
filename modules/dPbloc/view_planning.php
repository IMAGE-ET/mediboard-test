<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPbloc', 'plagesop'));
require_once( $AppUI->getModuleClass('dPhospi', 'affectation'));

$deb = mbGetValueFromGetOrSession("deb", mbDate());
$fin = mbGetValueFromGetOrSession("fin", mbDate());

$vide = dPgetParam( $_GET, 'vide', false );
$type = dPgetParam( $_GET, 'type', 0 );
$chir = dPgetParam( $_GET, 'chir', 0 );
$spe = dPgetParam( $_GET, 'spe', 0);
$salle = dPgetParam( $_GET, 'salle', 0 );
$CCAM = dPgetParam( $_GET, 'CCAM', '' );

//On sort les plages opératoires
//  Chir - Salle - Horaires

$plagesop = new CPlageOp;

$where = array();
$where["date"] =  "BETWEEN '$deb' AND '$fin'";

$order = array();
$order[] = "date";
$order[] = "id_salle";
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
  $where["id_salle"] = "= '$salle'";
}

$plagesop = $plagesop->loadList($where, $order);

// Operations de chaque plage
foreach($plagesop as $keyPlage => $valuePlage) {
  $plage =& $plagesop[$keyPlage];
  $plage->loadRefsFwd();
  
  $listOp = new COperation;
  $where = array();
  $where["plageop_id"] = "= '".$valuePlage->id."'";
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
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('plagesop', $plagesop);

$smarty->display('view_planning.tpl');

?>