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

// Liste des heures et minutes
$listHours_ = CPlageOp::$hours;
$listMins_ = CPlageOp::$minutes;

// Remplissage de la clé du tableau d'heures 
foreach($listHours_ as $key=>$hour){
	$listHours[$hour] = $hour;
}

foreach($listMins_ as $key=>$min){
	$listMins[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}


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
  $listPlages[$keyPlages]->getNbOperations();
  
  // Mémorisation dans le tableau d'affichage
  $nbquartheure = ($valPlages->_heurefin-$valPlages->_heuredeb)*4;
  $nbquartheure = $nbquartheure - array_search($valPlages->_minutedeb,$listMins) + array_search($valPlages->_minutefin,$listMins);
  $valPlages->_nbQuartHeure = $nbquartheure;
  $arrayAffichage[$valPlages->salle_id."-".intval($valPlages->_heuredeb).":".$valPlages->_minutedeb] = $valPlages;
  
  // Détermination des horaire non vides
  $heure_encours = array_search(intval($valPlages->_heuredeb),$listHours);
  $min_encours   = array_search($valPlages->_minutedeb,$listMins);
  
  
  $dans_plage = true;
  
  if($valPlages->_heurefin>CPlageOp::$hours_stop && $valPlages->_heuredeb<=CPlageOp::$hours_stop && $valPlages->_minutedeb<end(CPlageOp::$minutes)){
      $valPlages->_heurefin = CPlageOp::$hours_stop;
      $valPlages->_minutefin   = end(CPlageOp::$minutes);
  }elseif($valPlages->_heuredeb<CPlageOp::$hours_start && $valPlages->_heurefin>=CPlageOp::$hours_start && $valPlages->_minutefin>0){
      $valPlages->_heuredeb = CPlageOp::$hours_start;
      $valPlages->_minutedeb   = reset(CPlageOp::$minutes);;
  }elseif($valPlages->_heurefin>CPlageOp::$hours_stop || $valPlages->_heuredeb<CPlageOp::$hours_start){
      // Plages Hors semainier
      $dans_plage = false;
  }

  
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
$smarty = new CSmartyDP();

$smarty->assign("_temps_inter_op", $_temps_inter_op);
$smarty->assign("listSalles"     , $listSalles    );
$smarty->assign("listHours"      , $listHours     );
$smarty->assign("listMins"       , $listMins      );
$smarty->assign("arrayAffichage" , $arrayAffichage);
$smarty->assign("date"           , $date          );
$smarty->assign("listSpec"       , $listSpec      );
$smarty->assign("plagesel"       , $plagesel      );
$smarty->assign("specs"          , $specs         );
$smarty->assign("anesths"        , $anesths       );

$smarty->display("vw_edit_planning.tpl");
?>
