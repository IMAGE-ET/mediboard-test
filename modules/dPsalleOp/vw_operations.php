<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g, $dPconfig;

CAppUI::requireModuleFile("dPsalleOp", "inc_personnel");

$can->needsRead();

$listPersAideOp = array();
$listPersPanseuse = array();

// Ne pas supprimer, utilisé pour mettre le particien en session
$praticien_id    = mbGetValueFromGetOrSession("praticien_id");
$salle           = mbGetValueFromGetOrSession("salle");
$op              = mbGetValueFromGetOrSession("op");
$date            = mbGetValueFromGetOrSession("date", mbDate());
$date_now        = mbDate();
$modif_operation = (CAppUI::conf("dPsalleOp COperation modif_actes") == "never") ||
                   ((CAppUI::conf("dPsalleOp COperation modif_actes") == "oneday") && ($date >= $date_now));

// Tableau d'affectations
$tabPersonnel = array();

// Creation du tableau de timing pour les affectations  
$timingAffect = array();

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

// Creation du tableau de timing pour les affectations  
$timingAffect = array();
  
// Opération selectionnée
$selOp = new COperation;
$timing = array();
$prescription = new CPrescription();
$protocoles = array();
$anesth_id = "";

if ($op) {
  $selOp->load($op);
  
  $selOp->loadRefs();
  $modif_operation = $modif_operation || (CAppUI::conf("dPsalleOp COperation modif_actes") == "button" && !$selOp->_ref_plageop->actes_locked);
//  $actesup = $selOp->_ref_actes_ccam->_ref_exec

  $sejour =& $selOp->_ref_sejour;
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->_ref_dossier_medical->loadRefsBack();
  $sejour->loadRefsConsultAnesth();
  $sejour->loadRefsPrescriptions();
  $sejour->_ref_consult_anesth->loadRefsFwd();

  // Chargement des consultation d'anesthésie pour les associations a posteriori
  $patient =& $sejour->_ref_patient;
  $patient->loadRefsConsultations();
  $patient->loadRefPhotoIdentite();
  foreach ($patient->_ref_consultations as $consultation) {
    $consultation->loadRefConsultAnesth();
    $consult_anesth =& $consultation->_ref_consult_anesth;
    if ($consult_anesth->_id) {
      $consultation->loadRefPlageConsult();
      $consult_anesth->loadRefOperation();
    }
  }

  $selOp->getAssociationCodesActes();
  $selOp->loadExtCodesCCAM();
  $selOp->loadPossibleActes();
  
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
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $selOp->$key !== null; $i++) {
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

	// Chargement des affectations de personnel pour la plageop et l'intervention
  loadAffectations($selOp, $tabPersonnel, $listPersAideOp, $listPersPanseuse, $timingAffect);
  
  // Chargement de la prescription de sejour
  $prescription->object_id = $selOp->sejour_id;
	$prescription->object_class = "CSejour";
	$prescription->type = "sejour";
	$prescription->loadMatchingObject();
	
	$anesth_id = ($selOp->anesth_id) ? $selOp->anesth_id : $selOp->_ref_plageop->anesth_id;
	if($anesth_id && CModule::getActive('dPprescription')){
	  $protocoles = CPrescription::loadAllProtocolesFor($anesth_id, null, null, 'CSejour');
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
$acte_ngap->loadListExecutants();

// Vérification de la check list journalière
$check_list = CDailyCheckList::getTodaysList($salle);
$check_list->loadItemTypes();
$check_list->loadBackRefs('items');
$check_item_category = new CDailyCheckItemCategory;
$check_item_categories = $check_item_category->loadList(null);

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;
$smarty->assign("unites",$unites);

if ($selOp->_id){
  $smarty->assign("listChamps", $listChamps);
}

$smarty->assign("acte_ngap"       , $acte_ngap               );
$smarty->assign("op"              , $op                      );
$smarty->assign("salle"           , $salle                   );
$smarty->assign("listAnesthType"  , $listAnesthType          );
$smarty->assign("listAnesths"     , $listAnesths             );
$smarty->assign("listChirs"       , $listChirs               );
$smarty->assign("modeDAS"         , $dPconfig["dPsalleOp"]["CDossierMedical"]["DAS"]);
$smarty->assign("selOp"           , $selOp                   );
$smarty->assign("timing"          , $timing                  );
$smarty->assign("date"            , $date                    );
$smarty->assign("modif_operation" , $modif_operation         );
$smarty->assign("tabPersonnel"    , $tabPersonnel            );
$smarty->assign("listPersAideOp"  , $listPersAideOp          );
$smarty->assign("listPersPanseuse", $listPersPanseuse        );
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("isImedsInstalled"       , CModule::getActive("dPImeds"));
$smarty->assign("timingAffect"    , $timingAffect            );
$smarty->assign("prescription"    , $prescription            );
$smarty->assign("protocoles"      , $protocoles              );
$smarty->assign("anesth_id"       , $anesth_id               );
$smarty->assign("check_list"      , $check_list              );
$smarty->assign("check_item_categories", $check_item_categories);
$smarty->display("vw_operations.tpl");

?>