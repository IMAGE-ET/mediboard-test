<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $m;
$fct_selected = CValue::get("fct_selected", null);
$_nb_anesth   = CValue::get("_nb_anesth"  , 0);
$_nb_cab      = CValue::get("_nb_cab"     , 0);

$creation_prat = intval($_nb_anesth) + intval($_nb_cab);
$listPrat = null;

if($fct_selected && is_array($fct_selected)){  
  $listPrat = new CMediusers;
  $where = array();
  $ljoin = array();
  $where["function_id"] = CSQLDataSource::prepareIn($fct_selected);
  $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";
  $order = "`users`.`user_last_name`, `users`.`user_first_name`";
  $listPrat = $listPrat->loadList($where, $order, null, null, $ljoin);
  
  foreach($listPrat as $keyPrat=>$prat){
   if(!$prat->isPraticien()){
     unset($listPrat[$keyPrat]);
   } 
  }
  $list_10 = CMbArray::createRange(0,10, true);
}else{
  $list_10 = CMbArray::createRange(1,10, true);
}

$list_50 = CMbArray::createRange(1,50, true);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_10"       , $list_10);
$smarty->assign("list_50"       , $list_50);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("creation_prat" , $creation_prat);

$smarty->display("inc_echantillonnage_etape3.tpl");
?>