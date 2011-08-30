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
$service_id 	= CValue::postOrSession("service_id");

//Chargement de tous les services
$chambre= new CChambre();
$services=$chambre->loadList(null,null,null,"service_id");
foreach($services as $ch){
	$ch->loadRefsFwd();
}
$les_chambres=null;

for($i=0;$i<100;$i++){
$les_chambres[$i]="null";
}
$chambres=null;
if($service_id!=""){
	$chambre= new CChambre();
	$where[]=" annule='0'";
	$where["service_id"]="= '$service_id'";
	$chambres=$chambre->loadList($where);
	foreach($chambres as $ch){
		$ch->loadRefsFwd();
		if($ch->plan!=null){$les_chambres[$ch->plan]=$ch;}
	}
}


$zone=null;
for ($a=0;$a<100;$a++){
	$zone[$a]=$a;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services"	, $services);
$smarty->assign("chambres"	, $chambres);
$smarty->assign("service_id"	, $service_id);
$smarty->assign("zones"	, $zone);
$smarty->assign("les_chambres"	, $les_chambres);

$smarty->display("vw_plan_etage.tpl");

?>