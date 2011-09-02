<?php  /* $Id: vw_plan_etage.php  $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération des paramètres
$service_id   = CValue::postOrSession("service_id");

//Chargement de tous les services
$service_selectionne = new CService();
$service_selectionne->load($service_id);

$chambre = new CChambre();
$services=$chambre->loadList(null,null,null,"service_id");
foreach($services as $ch){
  $ch->loadRefsFwd();
}

$grille = array_fill(0, 10, array_fill(0, 10, 0));

$chambres_non_placees = array();

if($service_id!=""){
	
  $chambre = new CChambre();
  $where["annule"] = "= '0'";
  $where["service_id"] = "= '$service_id'";
  
  $chambres=$chambre->loadList($where);
  
  foreach($chambres as $ch){
    $ch->loadRefsFwd();
    if($ch->plan_x != null && $ch->plan_y != null){
      $grille[$ch->plan_y][$ch->plan_x] = $ch;
    }
    else{
    	$chambres_non_placees[] = $ch;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services"              , $services);
$smarty->assign("chambres_non_placees"  , $chambres_non_placees);
$smarty->assign("service_id"            , $service_id);
$smarty->assign("service_selectionne"   , $service_selectionne);
$smarty->assign("grille"                , $grille);

$smarty->display("vw_plan_etage.tpl");

?>