<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$date = mbGetValueFromGetOrSession("date", mbDate());
$plageop_id = mbGetValueFromGetOrSession("plageop_id");

// Liste des Salles
$salle = new CSalle();
$where = array();
$where["group_id"] = "= '$g'";
$order = "'nom'";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);


// Informations sur la plage demandée
$plagesel = new CPlageOp;
$plagesel->load($plageop_id);
if($plagesel->plageop_id){
  $arrKeySalle = array_keys($listSalles);
  if(!in_array($plagesel->salle_id,$arrKeySalle) || $plagesel->date!=$date) {
    $plageop_id = 0;
    $plagesel = new CPlageOp;
  }
}


// Liste des Specialités
$function = new CFunctions;
$specs = $function->loadSpecialites(PERM_READ);
foreach($specs as $key => $spec) {
  $specs[$key]->loadRefsUsers(array("Chirurgien", "Anesthésiste"));
}

// Liste des Anesthésistes
$mediuser = new CMediusers;
$anesths = $mediuser->loadAnesthesistes();


$_temps_inter_op = range(0,59,15);

// Création du tableau de visualisation
$arrayAffichage = array();
foreach($listSalles as $keySalle=>$valSalle){
  foreach(CPlageOp::$hours as $keyHours=>$valHours){
    foreach(CPlageOp::$minutes as $keyMins=>$valMins){
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
  $listPlages[$keyPlages]->getNbOperations();
  
  
  // Plages pas totalement comprises dans le semainier
  if($valPlages->_heurefin > CPlageOp::$hours_stop){
      $valPlages->_heurefin = CPlageOp::$hours_stop;
      $valPlages->_minutefin = end(CPlageOp::$minutes);
  }
  if($valPlages->_heuredeb < CPlageOp::$hours_start){
      $valPlages->_heuredeb = CPlageOp::$hours_start;
      $valPlages->_minutedeb   = reset(CPlageOp::$minutes);
  }
  
  // Initialisation des variables 
  $outPlage = false;
  $dans_plage = true;
  
  // Cas des plages non comprises dans le semainier
  if($valPlages->_heurefin <= CPlageOp::$hours_start || $valPlages->_heuredeb >= CPlageOp::$hours_stop){
      $outPlage = true;
  }
  
  // si on est dans le semainier
  if(!$outPlage){ 	
    // Mémorisation dans le tableau d'affichage
    $nbquartheure = ($valPlages->_heurefin-$valPlages->_heuredeb)*4;
    $nbquartheure = $nbquartheure - array_search($valPlages->_minutedeb,CPlageOp::$minutes) + array_search($valPlages->_minutefin,CPlageOp::$minutes);
    $valPlages->_nbQuartHeure = $nbquartheure;
    $arrayAffichage[$valPlages->salle_id."-".intval($valPlages->_heuredeb).":".$valPlages->_minutedeb] = $valPlages;
 
  	// Détermination des horaire non vides
    $heure_encours = array_search(intval($valPlages->_heuredeb),CPlageOp::$hours);
    $min_encours   = array_search($valPlages->_minutedeb,CPlageOp::$minutes);
  
  	while($dans_plage == true){      
      $min_encours ++;
      if(!array_key_exists($min_encours,CPlageOp::$minutes)){
        $min_encours=0;
        $heure_encours ++;
        if(!array_key_exists($heure_encours,CPlageOp::$hours)){
          $heure_encours=CPlageOp::$hours_start;
        }
      }
      if($heure_encours == $valPlages->_heurefin && CPlageOp::$minutes[$min_encours] == $valPlages->_minutefin){
        $dans_plage = false;
      }else{
        $arrayAffichage[$valPlages->salle_id."-".$heure_encours.":".CPlageOp::$minutes[$min_encours]] = "full";       
      }
    } 
  }
}




// Liste des Spécialités
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();


//Création du template
$smarty = new CSmartyDP();

$smarty->assign("_temps_inter_op", $_temps_inter_op   );
$smarty->assign("listSalles"     , $listSalles        );
$smarty->assign("listHours"      , CPlageOp::$hours   );
$smarty->assign("listMins"       , CPlageOp::$minutes );
$smarty->assign("arrayAffichage" , $arrayAffichage    );
$smarty->assign("date"           , $date              );
$smarty->assign("listSpec"       , $listSpec          );
$smarty->assign("plagesel"       , $plagesel          );
$smarty->assign("specs"          , $specs             );
$smarty->assign("anesths"        , $anesths           );

$smarty->display("vw_edit_planning.tpl");
?>
