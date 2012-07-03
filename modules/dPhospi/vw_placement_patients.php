<?php  /* $Id: vw_placement_patients.php  $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération des paramètres
$service_id 	   = CValue::postOrSession("service_id");
$date  = CValue::getOrSession("date", mbDateTime());

//Chargement de tous les services
$service_selectionne = new CService();
$service_selectionne->load($service_id);


$chambre = new CChambre();
$services=$chambre->loadList(null,null,null,"service_id");
foreach($services as $ch){
  $ch->loadRefsFwd();
}

$grille = array_fill(0, 10, array_fill(0, 10, 0));
$chambres = array();
if($service_id!=""){
  
  $chambre = new CChambre();
  $where["annule"] = "= '0'";
  $where["service_id"] = "= '$service_id'";
  
  $chambres=$chambre->loadList($where);
  
  foreach($chambres as $ch){
    $ch->loadRefsFwd();
    $ch->loadRefsBack();
    if($ch->plan_x != null && $ch->plan_y != null){
      $grille[$ch->plan_y][$ch->plan_x] = $ch;
    }
  }
}

//Traitement des lignes vides
  $nb;  $total;
foreach($grille as $j => $value) {
	$nb=0;
	  foreach($value as $i => $valeur){
		if($valeur=="0")
		{
			if($j==0 || $j==9){
				$nb++;
			}
			else{
				if( !isset($grille[$j-1]) || $grille[$j-1][$i]=="0" || !isset($grille[$j+1]) || $grille[$j+1][$i]=="0" ){
					$nb++;
				}
			}
		}
	}
	//suppression des lignes inutiles
	if($nb==10){
    unset($grille[$j]);
	}
}

//Traitement des colonnes vides
for($i=0;$i<10;$i++) {
	$nb=0;
  $total=0;
  for($j=0;$j<10;$j++){
  	 $total++;
     if(!isset($grille[$j][$i]) || $grille[$j][$i]=="0")
     {
     	if($i==0 || $i==9){$nb++;}
     	else{
     		if((!isset($grille[$j][$i-1]) || $grille[$j][$i-1]=="0") || (!isset($grille[$j][$i+1]) || $grille[$j][$i+1]=="0")){
        $nb++;
     		}
     	}
     }
  }
  //suppression des colonnes inutiles
	if($nb==$total){
    for($a=0;$a<10;$a++){
	          unset($grille[$a][$i]);
    }
  }
}
	
$sejours=array();

$sejour=new CSejour();

$where=array();
$where["service_id"]="= '$service_id'";
$sejours=$sejour->loadList($where);
foreach($sejours as $sej){
	$sej->loadRefsFwd();
}

// Liste des services
$services = new CService;
$where = array();
$where["service_id"]="= '$service_id'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

//tous les services 
$service = new CService();
$les_services=$service->loadList();

$listAff = array(
   "Aff"    => array(),
   "NotAff" => array()
);
  
if($service_id){
  $group = CGroups::loadCurrent();
  $affectation = new CAffectation;
  $ljoin = array(
    "lit"     => "affectation.lit_id = lit.lit_id",
    "chambre" => "chambre.chambre_id = lit.chambre_id",
    "service" => "service.service_id = chambre.service_id",
    "sejour"  => "sejour.sejour_id   = affectation.sejour_id"
  );
  $where = array(
    "affectation.entree"  => "< '$date'",
    "affectation.sortie"  => "> '$date'",
    "service.service_id"  => CSQLDataSource::prepareIn(array_keys($services), null),
    "sejour.group_id"     => "= '$group->_id'"
  );
  $order = "service.nom, chambre.nom, lit.nom";
  $listAff["Aff"] = $affectation->loadList($where, $order, null, null, $ljoin);
  foreach($listAff["Aff"] as &$_aff) {
    $_aff->loadView();
    $_aff->loadRefSejour();
    $_aff->_ref_sejour->loadRefPatient();
    $_aff->_ref_sejour->loadComplete();
    $_aff->_ref_sejour->checkDaysRelative($date);
    $_aff->loadRefLit();
  }
    $sejour = new CSejour();
    
    $where = array(
      "sejour.entree"   => "< '$date'",
      "sejour.sortie"   => "> '$date'",
      "sejour.group_id" => "= '$group->_id'"
    );
    $order = "sejour.entree, sejour.sortie";
    $listAff["NotAff"] = $sejour->loadList($where, $order);
    foreach($listAff["NotAff"] as $key => $_sejour) {
    	$_sejour->loadRefsAffectations();
    	if(!empty($_sejour->_ref_affectations)){
    		 unset($listAff["NotAff"][$key]);//supression des sejour affectés
    	}
    	else{
      	$_sejour->loadRefPatient();
    	}
    	$_sejour->loadComplete();
    	$_sejour->checkDaysRelative($date);
    }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("les_services"          , $les_services);
$smarty->assign("chambres"              , $chambres);
$smarty->assign("service_id"            , $service_id);
$smarty->assign("sejours"	              , $sejours);
$smarty->assign("date"                  , $date);
$smarty->assign("suiv"                  , mbDate("+1 day", $date));
$smarty->assign("prec"                  , mbDate("-1 day", $date));
$smarty->assign("chambres_affectees"    , $listAff["Aff"]);
$smarty->assign("chambre_non_affectees" , $listAff["NotAff"]);
$smarty->assign("services"              , $services);
$smarty->assign("grille"                , $grille);
$smarty->assign("service_selectionne"   , $service_selectionne);

$smarty->display("vw_placement_patients.tpl");

?>