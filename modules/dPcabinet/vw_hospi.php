<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPCabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Récupération des paramètres
$chirSel = mbGetValueFromGetOrSession("chirSel");
$dateRecherche = mbGetValueFromGetOrSession("dateRecherche", mbDate());

// Liste des chirurgiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Recherche des patients du praticien

$boucle_req=array("entree" => "listEntree","sortie" =>"listSortie");

foreach ($boucle_req as $keyBoucleReq => $curr_BoucleReq){
  $sql = "SELECT affectation.*" .
		 "\nFROM affectation" .
		 "\nLEFT JOIN lit" .
		 "\nON affectation.lit_id = lit.lit_id" .
		 "\nLEFT JOIN chambre" .
		 "\nON chambre.chambre_id = lit.chambre_id" .
		 "\nLEFT JOIN service" .
		 "\nON service.service_id = chambre.service_id" .
		 "\nLEFT JOIN sejour" .
		 "\nON sejour.sejour_id = affectation.sejour_id" .
		 "\nWHERE affectation.$keyBoucleReq < '$dateRecherche 23:59:59'" .
		 "\nAND affectation.$keyBoucleReq > '$dateRecherche 00:00:00'" .
		 "\nAND sejour.praticien_id = '$chirSel'" .
		 "\nORDER BY affectation.$keyBoucleReq, service.nom, chambre.nom, lit.nom";

  ${"Aff".$curr_BoucleReq} = new CAffectation;
  ${"Aff".$curr_BoucleReq} = db_loadObjectList($sql, ${"Aff".$curr_BoucleReq});
  
  foreach(${"Aff".$curr_BoucleReq} as $key => $currAff) {
    $loadData = false;
    ${"Aff".$curr_BoucleReq}[$key]->loadRefs();
    if($keyBoucleReq=="entree"){
      if(!${"Aff".$curr_BoucleReq}[$key]->_ref_prev->affectation_id){
        $loadData = true;
      }
    }elseif($keyBoucleReq=="sortie"){
      if(!${"Aff".$curr_BoucleReq}[$key]->_ref_next->affectation_id){
        $loadData = true;
      }    
    }
    
    if($loadData){
      ${"Aff".$curr_BoucleReq}[$key]->_ref_sejour->loadRefsFwd();
      ${"Aff".$curr_BoucleReq}[$key]->_ref_sejour->loadRefsOperations();
      
      if($keyBoucleReq=="sortie"){
        // Rowspan pour la cellule de l'heure
        ${"Aff".$curr_BoucleReq}[$key]->_nb_rows =count(${"Aff".$curr_BoucleReq}[$key]->_ref_sejour->_ref_operations) + 1;
        // Récupération des Références pour les opérations
        foreach(${"Aff".$curr_BoucleReq}[$key]->_ref_sejour->_ref_operations as $keyOper => $curr_oper) {
          ${"Aff".$curr_BoucleReq}[$key]->_ref_sejour->_ref_operations[$keyOper]->loadRefs();
        }
      }
      
      ${"Aff".$curr_BoucleReq}[$key]->_ref_lit->loadCompleteView();
    }else{
      unset(${"Aff".$curr_BoucleReq}[$key]);
    }
  }

}

// récupération des modèles de compte-rendu disponibles
$where = array();
$where["chir_id"] = db_prepare("= %", $chirSel);
$crList    = CCompteRendu::loadModeleByCat("Opération", $where);
$hospiList = CCompteRendu::loadModeleByCat("Hospitalisation", $where);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("dateRecherche" , $dateRecherche);
$smarty->assign("chirSel"       , $chirSel       );
$smarty->assign("listPrat"      , $listPrat      );
$smarty->assign("AfflistEntree" , $AfflistEntree );
$smarty->assign("AfflistSortie" , $AfflistSortie );
$smarty->assign("crList"        , $crList        );
$smarty->assign("hospiList"     , $hospiList     );

$smarty->display("vw_hospi.tpl");

?>