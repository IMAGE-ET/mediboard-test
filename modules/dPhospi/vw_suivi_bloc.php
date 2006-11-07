<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPospi
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$service_id = mbGetValueFromGetOrSession("service_id", null); 
$date_suivi = mbGetValueFromGetOrSession("date_suivi", mbDate());
$listOps = array();

// Liste des services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

$serviceSel = new CService;
if($serviceSel->load($service_id)){
  if($serviceSel->group_id!=$g || !$serviceSel->canRead()){
    $serviceSel = new CService;
  }else{
    // Liste des lits du service
    $table    = "lit";
    $select   = "lit_id";
    $leftjoin = array("chambre"     => "chambre.chambre_id = lit.chambre_id");
    $where    = array("service_id"=>"= '$serviceSel->service_id'");
    $request  = new CRequest();
    $request->addTable($table);
    $request->addSelect($select);
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $resultLit = db_loadList($request->getRequest());
    $listLit = array();
    foreach($resultLit as $aLit){
      $listLit[] = $aLit["lit_id"];	
    }
    
    // Selection des plages opératoires de la journée
    $plages = new CplageOp;
    $where = array();
    $where["date"] = "= '$date_suivi'";
    $plages = $plages->loadList($where);
    
    // Listes des opérations
    $listOps = new COperation;
    $where = array();
    $where[] = "`plageop_id` ".db_prepare_in(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date_suivi')";
    $order = "time_operation";
    $listOps = $listOps->loadList($where,$order);
    foreach($listOps as $key => $value) {
      $oper =& $listOps[$key];
      // Chargement des infos de l'opération
      $oper->loadRefSejour();
      $oper->_ref_sejour->loadRefsFwd();
      $oper->_ref_sejour->loadRefsAffectations();   
      if(count($oper->_ref_sejour->_ref_affectations)){
        $dans_service = false;
        foreach($oper->_ref_sejour->_ref_affectations as $keyAff=>$currAff){
          $affect =& $oper->_ref_sejour->_ref_affectations[$keyAff];          
          if(mbDate($affect->entree) <= $date_suivi && mbDate($affect->sortie) >= $date_suivi && in_array($affect->lit_id,$listLit)){
          	$dans_service = true;
          }
        }
        if(!$dans_service){
          unset($listOps[$key]);
        }
      }else{
        unset($listOps[$key]);
      }
    }
  }
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("date_suivi"  , $date_suivi);
$smarty->assign("listOps"     , $listOps);
$smarty->assign("service_id"  , $service_id);
$smarty->assign("serviceSel"  , $serviceSel);
$smarty->assign("services"    , $services);

$smarty->display("vw_suivi_bloc.tpl");
?>