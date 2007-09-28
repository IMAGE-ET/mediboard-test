<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$date = mbGetValueFromGetOrSession("date", mbDate());

$date = mbDate("last sunday", $date);
$fin  = mbDate("next sunday", $date);
$date = mbDate("+1 day", $date);

// Liste des jours
$listDays = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}


$plagesel = new CPlageOp;
// Liste des heures et minutes
$listHours_ = CPlageop::$hours;
$listMins_ = CPlageop::$minutes;
 
foreach($listHours_ as $key=>$hour){
	$listHours[$hour] = $hour;
}
foreach($listMins_ as $key=>$min){
	$listMins[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}


// Liste des Salles
$salle = new CSalle();
$where = array();
$where["group_id"] = "= '$g'";
$order = "'nom'";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);

// Création du tableau de visualisation
$arrayAffichage = array();
foreach($listDays as $keyDate=>$valDate){
  foreach($listSalles as $keySalle=>$valSalle){
    foreach($listHours as $keyHours=>$valHours){
      foreach($listMins as $keyMins=>$valMins){
        // Initialisation du tableau
        $arrayAffichage["$keyDate-s$keySalle-$valHours:$valMins"] = "empty";
      }
    }
  }
}
// Extraction des plagesOp par date
foreach($listDays as $keyDate=>$valDate){
  // Récupération des plages par jour
  $listPlages = new CPlageOp();
  $where = array();
  $where["date"] = "= '$keyDate'";
  $order = "debut";
  $listPlages = $listPlages->loadList($where,$order);
  foreach($listPlages as $keyPlages=>$valPlages){
    // Test validité des plages dans le semainier    
    $heure_fin = $valPlages->_heurefin;
    $heure_deb = $valPlages->_heuredeb;
    $min_deb   = $valPlages->_minutedeb;
    $min_fin   = $valPlages->_minutefin;
    $outPlage = false;
    foreach (array("min_deb","min_fin") as $minute){
      $minute_trouve = array_search(${$minute},$listMins);
      if ($minute_trouve===false){
        $afterValue = 0;
        foreach ($listMins as $valueMin){
          if (${$minute} > $valueMin && $afterValue!==null){
            $afterValue = $valueMin;
          } elseif($afterValue!==null){
            // Entre l'ancienne valeur et celle ci
            $centerValue = $afterValue + ($valueMin-$afterValue)/2;
            $afterValue = null;
            if(${$minute}>$centerValue){
              ${$minute} = $valueMin;
            }else{
              ${$minute} = $afterValue;
            }
          }
        }
        if($afterValue!==null){
          ${$minute} = $afterValue;
        }
      } 
    }
    if($heure_fin>16 && $heure_deb<=16 && $min_deb<45){
      $heure_fin = "16";
      $min_fin   = "45";
    }elseif($heure_deb<8 && (($heure_fin==8 && $min_fin>0) || $heure_fin>8)){
      $heure_deb = "08";
      $min_deb   = "00";
    }elseif($heure_fin>20 || $heure_deb<8){
      // Plages Hors semainier
      $outPlage = true;
    }
    
    if(!$outPlage){
      $listPlages[$keyPlages]->loadRefsFwd();
      $listPlages[$keyPlages]->_ref_chir->loadRefsFwd();
      $listPlages[$keyPlages]->getNbOperations();
      
      // Mémorisation dans le tableau d'affichage
      $nbquartheure = ($heure_fin-$heure_deb)*4;
      $nbquartheure = $nbquartheure - array_search($min_deb,$listMins) + array_search($min_fin,$listMins);
      $valPlages->_nbQuartHeure = $nbquartheure;
      
      $arrayAffichage["$keyDate-s".$valPlages->salle_id."-".intval($heure_deb).":".$min_deb] = $valPlages;
      // Détermination des horaire non vides
      $heure_encours = array_search(intval($heure_deb),$listHours);
      $min_encours   = array_search($min_deb,$listMins);
      $dans_plage = true;
      while($dans_plage == true){      
        $min_encours ++;
        if(!array_key_exists($min_encours,$listMins)){
          $min_encours=0;
          $heure_encours ++;
          if(!array_key_exists($heure_encours,$listHours)){
            $heure_encours=8;
          }
        }      
        if($heure_encours==$heure_fin && $listMins[$min_encours]==$min_fin){
          $dans_plage = false;
        }else{
          $arrayAffichage["$keyDate-s".$valPlages->salle_id."-".$heure_encours.":".$listMins[$min_encours]] = "full"; 
        }         
      }  
    }
  }  
}

// Liste des Spécialités
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();


//Création du template
$smarty = new CSmartyDP();

$smarty->assign("listDays"       , $listDays      );
$smarty->assign("listSalles"     , $listSalles    );
$smarty->assign("listHours"      , $listHours     );
$smarty->assign("listMins"       , $listMins      );
$smarty->assign("arrayAffichage" , $arrayAffichage);
$smarty->assign("date"           , $date          );
$smarty->assign("listSpec"       , $listSpec      );

$smarty->display("vw_planning_week.tpl");
?>
