<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

require_once($AppUI->getModuleClass("dPbloc"     , "plagesop"  ));
require_once($AppUI->getModuleClass("dPbloc"     , "salle"     ));
require_once($AppUI->getModuleClass("mediusers"  , "functions" ));
require_once($AppUI->getModuleClass("mediusers"));


$date = mbGetValueFromGetOrSession("date", mbDate());
$plageop_id = mbGetValueFromGetOrSession("plageop_id");

// Informations sur la plage demandée
$plagesel = new CPlageOp;
$plagesel->load($plageop_id);

// Liste des Specialités
$function = new CFunctions;
$specs = $function->loadSpecialites();

// Liste des Chirurgiens
$mediuser = new CMediusers;
$chirs = $mediuser->loadChirurgiens();
$anesths = $mediuser->loadAnesthesistes();

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
$where = array();
$where["group_id"] = "= '$g'";
$order = "'nom'";
$listSalles = $listSalles->loadList($where, $order);


// Création du tableau de visualisation
$arrayAffichage = array();
foreach($listSalles as $keySalle=>$valSalle){
  foreach($listHours as $keyHours=>$valHours){
    foreach($listMins as $keyMins=>$valMins){
      // Initialisation du tableau
      $arrayAffichage["$keySalle-$valHours:$valMins"] = "empty";
    }
  }
}

// Récupération des plages pour le jour demandé
$listPlages = new CPlageOp();
$where = array();
$where["date"] = "= '$date'";
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
  $arrayAffichage[$valPlages->salle_id."-".intval($valPlages->_heuredeb).":".$valPlages->_minutedeb] = $valPlages;
  
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
      $arrayAffichage[$valPlages->salle_id."-".$heure_encours.":".$listMins[$min_encours]] = "full";       
    }
  }  
}

// Liste des Spécialités
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();


//Création du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listSalles"     , $listSalles    );
$smarty->assign("listHours"      , $listHours     );
$smarty->assign("listMins"       , $listMins      );
$smarty->assign("arrayAffichage" , $arrayAffichage);
$smarty->assign("date"           , $date          );
$smarty->assign("listSpec"       , $listSpec      );
$smarty->assign("plagesel"       , $plagesel      );
$smarty->assign("specs"          , $specs         );
$smarty->assign("anesths"        , $anesths       );
$smarty->assign("chirs"          , $chirs         );

$smarty->display("vw_edit_planning.tpl");
?>
