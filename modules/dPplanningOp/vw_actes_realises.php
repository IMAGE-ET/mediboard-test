<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author Alexis Granger
 */


set_time_limit(180);

$_date_min = mbGetValueFromGetOrSession("_date_min");
$_date_max = mbGetValueFromGetOrSession("_date_max");
$chir_id = mbGetValueFromGetOrSession("chir_id");
$typeVue = mbGetValueFromGetOrSession("typeVue");

// Declaration des variables
$tarifPlage = array();
$tarifOperation = array();
$listOperations = array();
$nbActes = array();

// Initialisation des variables
$tarifTotal = 0;
$nbActeCCAM = 0;
$nbOperation = 0;

// Test pour savoir si le user recherché est un chir ou un anesth
$mediuser = new CMediusers();
$mediuser->load($chir_id);
if($mediuser->isFromType(array("Anesthésiste"))){
  $type="anesth_id";// anesthesiste
}
if($mediuser->isFromType(array("Chirurgien"))){
  $type="chir_id";
}


// Vue complete
if($typeVue == 1){
	// Recherche sur les plages op a l'aide des criteres fournis par l'utilisateur
	$plageop = new CPlageOp();
	$where["date"] = "BETWEEN '$_date_min' AND '$_date_max'";
	$where[$type] = " = '$chir_id'";
	$plagesop = $plageop->loadList($where);
	
	
	// recuperation de la liste des operations a partir des plagesop
	foreach($plagesop as $key => $plage){
	  // Initialisation
	  $tarifPlage[$plage->_id] = 0;
	  // parametre 0 pour ne pas selectionner les operations annulees
	  $plage->loadRefsBack(0);
	  $plage->loadRefSalle();
	  $listOperations[$plage->date][$plage->_id] = $plage;
	  // Parcours des operations de la plage
	  foreach($plage->_ref_operations as $key => $operation){
	    $nbOperation++;
	    $operation->loadRefSejour();
	    $operation->_ref_sejour->loadRefPatient();
	    $operation->loadRefPlageOp();
	    $operation->loadRefsActesCCAM();
	    $tarif_operation = 0;
	    $nbActes[$operation->_id] = 0;
	    foreach($operation->_ref_actes_ccam as $key => $acte_ccam){
	      if($acte_ccam->executant_id == $chir_id){
	        $nbActes[$operation->_id]++;
	        $tarif_operation += $acte_ccam->getTarif();
	      }
	    }
	    $tarifOperation[$operation->_id] = $tarif_operation;
	    $tarifPlage[$plage->_id] += $tarif_operation;   
	  }
	}
	
	
	// recuperation de la liste des operations en urgence
	$urgence = new COperation();
	$whereUrgence["date"] = "BETWEEN '$_date_min' AND '$_date_max'";
	$whereUrgence[$type] = "= '$chir_id'";
	$urgences = $urgence->loadList($whereUrgence);
	foreach($urgences as $key => $_urgence){
	  $nbOperation++;
	  $tarifPlage[$_urgence->_id] = 0;
	  $_urgence->loadRefSejour();
	  $_urgence->_ref_sejour->loadRefPatient();
	  $_urgence->loadRefsActesCCAM();
	  $tarif_operation = 0;
	  $nbActes[$_urgence->_id] = 0;
	  foreach($_urgence->_ref_actes_ccam as $key => $acte_ccam){
	    if($acte_ccam->executant_id == $chir_id){
	      $nbActes[$_urgence->_id]++;
	      $tarif_operation += $acte_ccam->getTarif();
	      $acte_ccam->getTarif();
	    }
	  }
	  $listOperations[$_urgence->date]["urgence"][$_urgence->_id] = $_urgence;
	  $tarifOperation[$_urgence->_id] = $tarif_operation;
	  $tarifPlage[$_urgence->_id] += $tarif_operation; 
	}
	
	// Calcul du total
	foreach($tarifPlage as $key => $value){
	  $tarifTotal += $value;
	}
	
	foreach($nbActes as $key => $value){
	  $nbActeCCAM += $value;
	}
	
	// Tri par date des plagesop et operations en urgence
	if($listOperations){
	  ksort($listOperations);
	}
}



// Vue des totaux
if($typeVue == 2){
  //Recherche des plagesop
  $plageop = new CPlageOp();
	$where["date"] = "BETWEEN '$_date_min' AND '$_date_max'";
	$where[$type] = " = '$chir_id'";
	$plagesop = $plageop->loadList($where);

	// Operations dans les plagesop
	foreach($plagesop as $key => $plage){
	  $plage->loadRefsBack(0);
	  foreach($plage->_ref_operations as $key => $operation){
      $nbOperation++;
	    $operation->loadRefsActesCCAM();
	    foreach($operation->_ref_actes_ccam as $key => $acte_ccam){
	      if($acte_ccam->executant_id == $chir_id){
	        $nbActeCCAM++;
	        $tarifTotal += $acte_ccam->getTarif();
	      }
	    }
	  }
	}
	
	// Operations en urgences (en dehors des plagesop)
	$urgence = new COperation();
	$whereUrgence["date"] = "BETWEEN '$_date_min' AND '$_date_max'";
	$whereUrgence[$type] = "= '$chir_id'";
	$urgences = $urgence->loadList($whereUrgence);
	foreach($urgences as $key => $_urgence){
	  $nbOperation++;
    $_urgence->loadRefsActesCCAM();
	  foreach($_urgence->_ref_actes_ccam as $key => $acte_ccam){
	    if($acte_ccam->executant_id == $chir_id){
	      $nbActeCCAM++;
	      $tarifTotal += $acte_ccam->getTarif();
	    }
	  }
	}
}


// Création du template
$smarty = new CSmartyDP();

// Vue complete
if($typeVue == 1){
  $smarty->assign("listOperations" , $listOperations );
  $smarty->assign("tarifOperation" , $tarifOperation );
  $smarty->assign("tarifPlage"     , $tarifPlage     );
  $smarty->assign("tarifTotal"     , $tarifTotal     );
  $smarty->assign("chir_id"        , $chir_id        );
  $smarty->assign("nbActes"        , $nbActes        );
}
$smarty->assign("praticien"     , $mediuser          );
$smarty->assign("debut"         , $_date_min         );
$smarty->assign("fin"           , $_date_max         );
$smarty->assign("typeVue"       , $typeVue           );
$smarty->assign("nbActeCCAM"    , $nbActeCCAM        );
$smarty->assign("nbOperation"   , $nbOperation       );
$smarty->assign("tarifTotal"    , $tarifTotal        );


$smarty->display("vw_actes_realises.tpl");

?>