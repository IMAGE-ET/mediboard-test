<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g, $dPconfig;

require_once($AppUI->getModuleFile("dPsalleOp", "inc_personnel"));

$can->needsRead();

$salle = mbGetValueFromGetOrSession("salle");
$op    = mbGetValueFromGetOrSession("op");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

// Tableau d'affectations
$tabPersonnel = array();

// Liste du personnel
$listPers = array();

// Creation du tableau de timing pour les affectations  
$timingAffect = array();
  

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

// Creation du tableau de timing pour les affectations  
$timingAffect = array();
  

// Selection des salles
$listSalles = new CSalle;
$where = array("group_id"=>"= '$g'");
$listSalles = $listSalles->loadList($where);

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = "= '$salle'";
$order = "debut";
$plages = $plages->loadList($where, $order);
foreach($plages as $key => $value) {
  $plages[$key]->loadRefs(0);
  foreach($plages[$key]->_ref_operations as $key2 => $value) {
    if($plages[$key]->_ref_operations[$key2]->rank == 0) {
      unset($plages[$key]->_ref_operations[$key2]);
    }
    else {
      $plages[$key]->_ref_operations[$key2]->loadRefSejour();
      $plages[$key]->_ref_operations[$key2]->_ref_sejour->loadRefPatient();
      $plages[$key]->_ref_operations[$key2]->loadRefsCodesCCAM();
    }
  }
}

$urgences = new COperation;
$where = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = "= '$salle'";
$order = "chir_id";
$urgences = $urgences->loadList($where);
foreach($urgences as $keyOp => $curr_op) {
  $urgences[$keyOp]->loadRefChir();
  $urgences[$keyOp]->loadRefSejour();
  $urgences[$keyOp]->_ref_sejour->loadRefPatient();
  $urgences[$keyOp]->loadRefsCodesCCAM();
}

// Opération selectionnée
$selOp = new COperation;
$timing = array();
if($op) {
  $selOp->load($op);
  
  $selOp->loadRefs();

  $selOp->_ref_sejour->loadRefDiagnosticPrincipal();
  $selOp->_ref_sejour->loadRefDossierMedical();
  $selOp->_ref_sejour->_ref_dossier_medical->loadRefsBack();

  $selOp->getAssociationCodesActes();
  
  foreach($selOp->_ext_codes_ccam as $keyCode => $code) {
    $selOp->_ext_codes_ccam[$keyCode]->Load();
  }
  $selOp->loadPossibleActes();
  /* Loading des comptes-rendus
  foreach($selOp->_ext_codes_ccam as $keyCode => $code) {
    foreach($code->activites as $keyActivite => $activite) {
      foreach($activite->phases as $keyPhase => $phase) {
        if($phase->_connected_acte->acte_id) {
          
        }
      }
    }
  }*/
  $selOp->_ref_plageop->loadRefsFwd();
  
  // Tableau des timings
  $timing["entree_salle"]    = array();
  $timing["pose_garrot"]     = array();
  $timing["debut_op"]        = array();
  $timing["fin_op"]          = array();
  $timing["retrait_garrot"]  = array();
  $timing["sortie_salle"]    = array();
  $timing["induction_debut"] = array();
  $timing["induction_fin"]   = array();
  
  foreach($timing as $key => $value) {
    for($i = -10; $i < 10 && $selOp->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $selOp->$key);
    }
  }

	// Affichage des données
	$listChamps = array(
	                1=>array("hb","ht","ht_final","plaquettes"),
	                2=>array("creatinine","_clairance","na","k"),
	                3=>array("tp","tca","tsivy","ecbu")
	                );
	$cAnesth =& $selOp->_ref_consult_anesth;
	foreach($listChamps as $keyCol=>$aColonne){
		foreach($aColonne as $keyChamp=>$champ){
		  $verifchamp = true;
	    if($champ=="tca"){
		    $champ2 = $cAnesth->tca_temoin;
		  }else{
		    $champ2 = false;
	      if(($champ=="ecbu" && $cAnesth->ecbu=="?") || ($champ=="tsivy" && $cAnesth->tsivy=="00:00:00")){
	        $verifchamp = false;
	      }
		  }
	    $champ_exist = $champ2 || ($verifchamp && $cAnesth->$champ);
	    if(!$champ_exist){
	      unset($listChamps[$keyCol][$keyChamp]);
	    }
		}
	}

	
	$selOp->_ref_consult_anesth->_ref_consultation->loadRefsBack();

	// récupération des modèles de compte-rendu disponibles
	$where                 = array();
	$order                 = "nom";
	$where["object_class"] = "= 'COperation'";
	$where["chir_id"]      = "= '$selOp->chir_id'";
	$crList                = CCompteRendu::loadModeleByCat("Opération", $where, $order, true);
	$hospiList             = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);
	
	// Packs d'hospitalisation
	$packList         = array();
	$where            = array();
	$where["object_class"] = " = 'COperation'";
	$where["chir_id"] = "= '$selOp->chir_id'";
	$pack             = new CPack;
	$packList         = $pack->loadlist($where, $order);
	
	// Chargement des affectations de personnel pour la plageop et l'intervention
  loadAffectations($selOp, $tabPersonnel, $listPers, $timingAffect);
 
  // Chargement de la liste du personnel pour l'operation
	$listPers = CPersonnel::loadListPers("op");
	    
	// Chargement du personnel affectée à la plage opératoire  
	$selOp->_ref_plageop->loadPersonnel();
	if ($selOp->_ref_plageop->_ref_personnel) {
		$tabPersonnel["plage"] = array();
		foreach($selOp->_ref_plageop->_ref_personnel as $key => $affectation_personnel){
		  // Chargement du personnel a partir des affectations      
		  //  $tabPersonnel[$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
		  $affectation = new CAffectationPersonnel();
		  $affectation->object_class = "COperation";
		  $affectation->object_id    = $selOp->_id;
		  $affectation->personnel_id = $affectation_personnel->_ref_personnel->_id;
		  $affectation->loadMatchingObject();
		  $affectation->loadPersonnel();
		  $affectation->_ref_personnel->loadRefUser();
		  $tabPersonnel["plage"][$affectation_personnel->_ref_personnel->_id] = $affectation;
		}
	}
		// Chargement du personnel non present dans la plageop (rajouté dans l'operation)
		$selOp->loadPersonnel();
		$tabPersonnel["operation"] = array();
		foreach($selOp->_ref_personnel as $key => $affectation_personnel){
		// Si le personnel n'est pas deja present dans le tableau d'affectation, on le rajoute
		  if((!array_key_exists($affectation_personnel->_ref_personnel->_id, $tabPersonnel["plage"])) && $affectation_personnel->_ref_personnel->emplacement == "op"){
		    $affectation_personnel->_ref_personnel->loadRefUser();
		    $tabPersonnel["operation"][$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
		  }
		}
		
		
	  // Suppression de la liste des personnels deja presents
		foreach($listPers as $key => $pers){
		  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
		    unset($listPers[$key]);
		  }
		}
		
		
	  // Initialisations des tableaux de timing
	  foreach($tabPersonnel as $key_type => $type_affectation){
	    foreach($type_affectation as $key => $affectation){
	      $timingAffect[$affectation->_id]["_debut"] = array();
	      $timingAffect[$affectation->_id]["_fin"] = array();
	    }
	  }
	
	  // Remplissage tu tableau de timing
	  foreach($tabPersonnel as $cle => $type_affectation){
	    foreach($type_affectation as $cle_type =>$affectation){
	      foreach($timingAffect[$affectation->_id] as $key => $value){
	        for($i = -10; $i < 10 && $affectation->$key !== null; $i++) {
	          $timingAffect[$affectation->_id][$key][] = mbTime("$i minutes", $affectation->$key);
	        }  
	      } 
	    }
	  }
}

$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);

//Tableau d'unités
$unites = array();
$unites["hb"]         = array("nom"=>"Hb","unit"=>"g/dl");
$unites["ht"]         = array("nom"=>"Ht","unit"=>"%");
$unites["ht_final"]   = array("nom"=>"Ht final","unit"=>"%");
$unites["plaquettes"] = array("nom"=>"Plaquettes","unit"=>"");
$unites["creatinine"] = array("nom"=>"Créatinine","unit"=>"mg/l");
$unites["_clairance"] = array("nom"=>"Clairance de Créatinine","unit"=>"ml/min");
$unites["na"]         = array("nom"=>"Na+","unit"=>"mmol/l");
$unites["k"]          = array("nom"=>"K+","unit"=>"mmol/l");
$unites["tp"]         = array("nom"=>"TP","unit"=>"%");
$unites["tca"]        = array("nom"=>"TCA","unit"=>"s");
$unites["tsivy"]      = array("nom"=>"TS Ivy","unit"=>"");
$unites["ecbu"]       = array("nom"=>"ECBU","unit"=>"");

// Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;
$smarty->assign("unites",$unites);

if($selOp->_id){
  $smarty->assign("listChamps", $listChamps);
  $smarty->assign("crList", $crList);
  $smarty->assign("hospiList", $hospiList);
  $smarty->assign("packList", $packList);
}

$smarty->assign("acte_ngap"       , $acte_ngap               );
$smarty->assign("op"              , $op                      );
$smarty->assign("vueReduite"      , false                    );
$smarty->assign("salle"           , $salle                   );
$smarty->assign("listSalles"      , $listSalles              );
$smarty->assign("listAnesthType"  , $listAnesthType          );
$smarty->assign("listAnesths"     , $listAnesths             );
$smarty->assign("listChirs"       , $listChirs               );
$smarty->assign("plages"          , $plages                  );
$smarty->assign("urgences"        , $urgences                );
$smarty->assign("modeDAS"         , $dPconfig["dPsalleOp"]["CDossierMedical"]["DAS"]);
$smarty->assign("selOp"           , $selOp                   );
$smarty->assign("timing"          , $timing                  );
$smarty->assign("date"            , $date                    );
$smarty->assign("modif_operation" , $modif_operation         );
$smarty->assign("tabPersonnel"    , $tabPersonnel            );
$smarty->assign("listPers"        , $listPers                );
$smarty->assign("timingAffect"    , $timingAffect);
$smarty->display("vw_operations.tpl");

?>