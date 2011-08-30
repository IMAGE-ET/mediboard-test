<?php  /* $Id: vw_placement_patients.php  $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

global $can, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

// Récupération des paramètres
$service_id 	= CValue::postOrSession("service_id");

$date_recherche = CValue::getOrSession("date_recherche", mbDateTime());

//Chargement de tous les services
$chambre= new CChambre();
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
//Traitement des lignes vides
for($j=1;$j<=10;$j++){
	$nb=0;
	for($i=($j-1)*10;$i<(10*$j);$i++){
		if($les_chambres[$i]=="null"){$nb++;}
	}
	//suppression des lignes inutiles
	if($nb==10){
		for($i=($j-1)*10;$i<(10*$j);$i++){
			if($i<10 || $i>89 ){
					$les_chambres[$i]="0";
			}
			else{
				if($les_chambres[$i+10]!="null" && $les_chambres[$i+10]!="0" && $les_chambres[$i-10]!="null" && $les_chambres[$i-10]!="0"){
					$les_chambres[$i]="null";
				}
				else{
					$les_chambres[$i]="0";
				}
			}
			
		}
	}
}

//Traitement des colonnes vides
for($j=0;$j<10;$j++){
	$nb=0;
	$total=0;
	for($i=0;$i<10;$i++){
		$a=$i.$j+1-1;
		if($les_chambres[$a]!="0"){
			$total++;
			if($les_chambres[$a]=="null"){$nb++;}
		}
	}
	//suppression des lignes inutiles
	if($nb==$total){
		for($i=0;$i<10;$i++){
			$a=$i.$j+1-1;
			if($a%10==0 || $a%10==9 ){
					$les_chambres[$a]="0";
			}
			else{
				if($les_chambres[$a+1]!="null" && $les_chambres[$a+1]!="0" && $les_chambres[$a-1]!="null" && $les_chambres[$a-1]!="0"){
					$les_chambres[$a]="null";
				}
				else{
					$les_chambres[$a]="0";
				}
			}
			
		}
	}
}

$zone=null;
for ($a=0;$a<100;$a++){
	$zone[$a]=$a;
}

$sejours=null;

$sejour=new CSejour();

$where=null;
$where["service_id"]="= '$service_id'";
$sejours=$sejour->loadList($where);
foreach($sejours as $sej){
	$sej->loadRefsFwd();
}

// Liste des chirurgiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Liste des services
$services = new CService;
$where = array();
$where["service_id"]="= '$service_id'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

//tous les services 
$servic = new CService();
$les_services=$servic->loadList();

$listAff = null;
//
// Cas de l'affichage des lits d'un praticien
//
if($service_id){
  // Recherche des patients du praticien
  // Qui ont une affectation
  $listAff = array(
   "Aff"    => array(),
   "NotAff" => array()
  );
  $affectation = new CAffectation;
  $ljoin = array(
    "lit"     => "affectation.lit_id = lit.lit_id",
    "chambre" => "chambre.chambre_id = lit.chambre_id",
    "service" => "service.service_id = chambre.service_id",
    "sejour"  => "sejour.sejour_id   = affectation.sejour_id"
  );
  $where = array(
    "affectation.entree"  => "< '$date_recherche'",
    "affectation.sortie"  => "> '$date_recherche'",
    "service.service_id"  => CSQLDataSource::prepareIn(array_keys($services), null),
    "sejour.praticien_id" => CSQLDataSource::prepareIn(array_keys($listPrat), null),
    "sejour.group_id"     => "= '$g'"
  );
  $order = "service.nom, chambre.nom, lit.nom";
  $listAff["Aff"] = $affectation->loadList($where, $order, null, null, $ljoin);
  foreach($listAff["Aff"] as &$_aff) {
    $_aff->loadView();
    $_aff->loadRefSejour();
    $_aff->_ref_sejour->loadRefPatient();
    $_aff->_ref_sejour->_ref_praticien =& $listPrat[$_aff->_ref_sejour->praticien_id];

    $_aff->loadRefLit();
    $_aff->_ref_lit->loadCompleteView();
  }
    $sejour = new CSejour();
    $where = array(
      "sejour.entree"  => "< '$date_recherche'",
      "sejour.sortie"  => "> '$date_recherche'",
      "sejour.praticien_id" => CSQLDataSource::prepareIn(array_keys($listPrat), null),
      "sejour.group_id"     => "= '$g'"
    );
    $order = "sejour.entree, sejour.sortie, sejour.praticien_id";
    $listAff["NotAff"] = $sejour->loadList($where, $order);
    foreach($listAff["NotAff"] as &$_sejour) {
    	$_sejour->loadRefPatient();
      $_sejour->_ref_praticien =& $listPrat[$_sejour->praticien_id];
    }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("les_services"	, $les_services);
$smarty->assign("chambres"	    , $chambres);
$smarty->assign("service_id"    , $service_id);
$smarty->assign("zones"	        , $zone);
$smarty->assign("les_chambres"	, $les_chambres);
$smarty->assign("sejours"	      , $sejours);
$smarty->assign("date_recherche", $date_recherche);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("listAff"       , $listAff);
$smarty->assign("services"      , $services);

$smarty->display("vw_placement_patients.tpl");

?>