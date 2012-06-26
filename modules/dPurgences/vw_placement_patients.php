<?php  /* $Id: vw_placement_patients.php  $ */

/**
 * @package Mediboard
 * @subpackage urgences
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération des paramètres
$date           = CValue::getOrSession("date", mbDate());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before    = mbDate("-$date_tolerance DAY", $date);
$date_after     = mbDate("+1 DAY", $date);

//recherche des chambres d'urgences placées
$chambre = new CChambre();
$ljoin = array();
$ljoin["service"]     = "service.service_id = chambre.service_id";
$ljoin["emplacement"] = "emplacement.chambre_id = chambre.chambre_id";
$where = array();
$where["service.urgence"]    = "= '1'";
$where["emplacement.plan_x"] = "IS NOT NULL";
$chambres_urgences = $chambre->loadList($where, null, null, null, $ljoin);

$where = array();
$where["service.uhcd"]       = "= '1'";
$where["emplacement.plan_x"] = "IS NOT NULL";
$chambres_uhcd = $chambre->loadList($where, null, null, null, $ljoin);

$grille = array(
  "urgence" => array_fill(0, 10, array_fill(0, 10, 0)),
  "uhcd"    => array_fill(0, 10, array_fill(0, 10, 0))
);

$listSejours = array(
  "uhcd"    => array(),
  "urgence" => array(),
  );

$ljoin = array();
$ljoin["rpu"] = "rpu.sejour_id = sejour.sejour_id";
$temp = array();
$temp["sejour.type"]      = " = 'urg'";
$temp["sejour.entree"]    = " BETWEEN '$date_before' AND '$date_after'";
$temp["sejour.annule"]    = " = '0'";
$temp["sejour.group_id"]  = "= '".CGroups::loadCurrent()->_id."'";

for($num = 0; $num <= 1; $num++){
	if($num == 0){
    $chambres = $chambres_uhcd;
    $temp["sejour.uhcd"] = " = '1'";
    $nom = "uhcd";
	}else{
    $chambres = $chambres_urgences;
    $temp["sejour.uhcd"] = " = '0'";
    $nom = "urgence";
	}
	
  foreach($chambres as $chambre){
    $chambre->loadRefsFwd();
    $chambre->loadRefsLits();
    $chambre->loadRefEmplacement();
    $grille[$nom][$chambre->_ref_emplacement->plan_y][$chambre->_ref_emplacement->plan_x] = $chambre;
    $emplacement = $chambre->_ref_emplacement;
    if($emplacement->hauteur-1){
      for($a = 0; $a <= $emplacement->hauteur-1; $a++){
        if($emplacement->largeur-1){
         for($b = 0; $b <= $emplacement->largeur-1; $b++){
           if($b != 0){
             unset($grille[$nom][$emplacement->plan_y+$a][$emplacement->plan_x+$b]);
           }
           elseif($a != 0){
             unset($grille[$nom][$emplacement->plan_y+$a][$emplacement->plan_x+$b]);
           }
          }
        }
        elseif($a < $emplacement->hauteur-1){
          $c = $a+1;
          unset($grille[$nom][$emplacement->plan_y+$c][$emplacement->plan_x]);
        }
      }
    }
    elseif($emplacement->largeur-1){
      for($b = 1; $b <= $emplacement->largeur-1; $b++){
        unset($grille[$nom][$emplacement->plan_y][$emplacement->plan_x+$b]);
      }
    }
    $nb_lits=0;
    $q = "";
    foreach($chambre->_ref_lits as $lit){
    	if($nb_lits){
    		$q .= " OR ";
    	}
      $q .= "rpu.box_id = '".$lit->_id."'";
      $nb_lits++;
    }
    
    $where = array();
    $where = $temp;
    $where[] = $q;
    $sejour = new CSejour();
    $sejours = $sejour->loadList($where, null, null,null, $ljoin);
    if($sejours){
    	foreach($sejours as $sejour){
    		$sejour->loadRefRPU();
    		$sejour->loadRefPrescriptionSejour();
    		$sejour->loadRefsDocItems();
    	}
      $listSejours[$nom][$chambre->_id] = $sejours;
    }
  }
  
  //Traitement des lignes vides
  $nb=0;  $total=0;
  foreach($grille[$nom] as $j => $value) {
    $nb=0;
      foreach($value as $i => $valeur){
      if($valeur == "0"){
        if($j == 0 || $j == 9){
          $nb++;
        }
        else{
          if( !isset($grille[$nom][$j-1][$i]) || $grille[$nom][$j-1][$i] == "0" || !isset($grille[$nom][$j+1][$i]) || $grille[$nom][$j+1][$i] == "0" ){
            $nb++;
          }
        }
      }
    }
    //suppression des lignes inutiles
    if($nb == 10){
      unset($grille[$nom][$j]);
    }
  }
  
  //Traitement des colonnes vides
  for($i = 0; $i < 10; $i++) {
    $nb = 0;
    $total = 0;
    for($j = 0; $j < 10; $j++){
      $total++;
      if(!isset($grille[$nom][$j][$i]) || $grille[$nom][$j][$i] == "0"){
        if($i == 0 || $i == 9){ $nb++; }
        else{
          if ( (!isset($grille[$nom][$j][$i-1]) || $grille[$nom][$j][$i-1] == "0") || (!isset($grille[$nom][$j][$i+1]) || $grille[$nom][$j][$i+1] == "0")){
          $nb++;
          }
        }
      }
    }
    //suppression des colonnes inutiles
    if($nb == $total){
      for($a = 0; $a < 10; $a++){
       unset($grille[$nom][$a][$i]);
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
//
$smarty->assign("listSejours"    , $listSejours);
$smarty->assign("grilles"        , $grille);
$smarty->assign("date"           , $date);
$smarty->assign("suiv"           , mbDate("+1 day", $date));
$smarty->assign("prec"           , mbDate("-1 day", $date));

$smarty->display("vw_placement_patients.tpl");
?>