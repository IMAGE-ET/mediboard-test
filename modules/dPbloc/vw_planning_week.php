<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPbloc"     , "plagesop"  ));
require_once($AppUI->getModuleClass("dPbloc"     , "salle"     ));
require_once($AppUI->getModuleClass("mediusers"  , "functions" ));


$date = mbGetValueFromGetOrSession("date", mbDate());

$date = mbDate("last sunday", $date);
$fin   = mbDate("next sunday", $date);
$date = mbDate("+1 day", $date);

// Liste des jours
$listDays = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}

// Liste des heures
$listHours = array();
for($i = 8; $i <= 20; $i++) {
  $listHours[$i] = $i;
}

// Liste des minutes
$listMins = array();
$listMins[] = "00";
$listMins[] = "15";
$listMins[] = "30";
$listMins[] = "45";

// Liste des Salles
$listSalles = new CSalle();
$order = "'nom'";
$listSalles = $listSalles->loadList(null,$order);

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
  $where["date"] = "= '$keyDate'";
  $order = "debut";
  $listPlages = $listPlages->loadList($where,$order);
  foreach($listPlages as $keyPlages=>$valPlages){
    $listPlages[$keyPlages]->loadRefsFwd();
    $listPlages[$keyPlages]->_ref_chir->loadRefsFwd();
    
    $listPlages[$keyPlages]->GetNbOperations();

    // Mémorisation dans le tableau d'affichage
    $nbquartheure = ($valPlages->_heurefin-$valPlages->_heuredeb)*4;
    $nbquartheure = $nbquartheure - array_search($valPlages->_minutedeb,$listMins) + array_search($valPlages->_minutefin,$listMins);
    $valPlages->_nbQuartHeure = $nbquartheure;


    $arrayAffichage["$keyDate-s".$valPlages->salle_id."-".intval($valPlages->_heuredeb).":".$valPlages->_minutedeb] = $valPlages;
    // Détermination des horaire non vides
    $heure_encours = array_search(intval($valPlages->_heuredeb),$listHours);
    $min_encours   = array_search($valPlages->_minutedeb,$listMins);    
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
      if($heure_encours==$valPlages->_heurefin && $listMins[$min_encours]==$valPlages->_minutefin){
        $dans_plage = false;
      }else{
        $arrayAffichage["$keyDate-s".$valPlages->salle_id."-".$heure_encours.":".$listMins[$min_encours]] = "full";	
      }         
    }
    
  }  
}

// Liste des Spécialités
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();


//Création du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listDays"       , $listDays      );
$smarty->assign("listSalles"     , $listSalles    );
$smarty->assign("listHours"      , $listHours     );
$smarty->assign("listMins"       , $listMins      );
$smarty->assign("arrayAffichage" , $arrayAffichage);
$smarty->assign("date"           , $date          );
$smarty->assign("listSpec"       , $listSpec      );

$smarty->display("vw_planning_week.tpl");
?>
