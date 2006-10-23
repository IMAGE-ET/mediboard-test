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
$chirSel   = mbGetValueFromGetOrSession("chirSel");
$date      = mbGetValueFromGetOrSession("date", mbDate());
$typeHospi = mbGetValueFromGet("typeHospi", "entree");
$board     = mbGetValueFromGet("board", 0);

$sql = "SELECT `affectation`.*" .
   "\nFROM `affectation`" .
   "\nLEFT JOIN `lit`" .
   "\nON `affectation`.`lit_id` = `lit`.`lit_id`" .
   "\nLEFT JOIN `chambre`" .
   "\nON `chambre`.`chambre_id` = `lit`.`chambre_id`" .
   "\nLEFT JOIN `service`" .
   "\nON `service`.`service_id` = `chambre`.`service_id`" .
   "\nLEFT JOIN `sejour`" .
   "\nON `sejour`.`sejour_id` = `affectation`.`sejour_id`" .
   "\nWHERE `affectation`.`$typeHospi` < '$date 23:59:59'" .
   "\nAND `affectation`.`$typeHospi` > '$date 00:00:00'" .
   "\nAND `sejour`.`praticien_id` = '$chirSel'" .
   "\nORDER BY `affectation`.`$typeHospi`, `service`.`nom`, `chambre`.`nom`, `lit`.`nom`";

$listAff = new CAffectation;
$listAff = db_loadObjectList($sql, $listAff);
  
foreach($listAff as $key => &$affectation) {
  $loadData = false;
  $affectation->loadRefs();
  
  if ($typeHospi == "entree") $affectation_connexe =& $affectation->_ref_prev;
  if ($typeHospi == "sortie") $affectation_connexe =& $affectation->_ref_next;

  if (!$affectation_connexe->_id) {
    $sejour =& $affectation->_ref_sejour;
    $sejour->loadRefsFwd();
    $sejour->loadRefsOperations();
      
    if ($typeHospi=="sortie"){
      // Rowspan pour la cellule de l'heure
      $affectation->_nb_rows = count($sejour->_ref_operations) + 1;
      // Récupération des Références pour les opérations
      foreach($sejour->_ref_operations as &$operation) {
        $operation->loadRefs();
      }
    }

    $affectation->_ref_lit->loadCompleteView();
  } else {
    unset($listAff[$key]);
  }
}

// récupération des modèles de compte-rendu disponibles
$where = array();
$order = "nom";
$where["object_class"] = "= 'COperation'";
$where["chir_id"] = db_prepare("= %", $chirSel);
$crList    = CCompteRendu::loadModeleByCat("Opération", $where, $order, true);
$hospiList = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("board"    , $board);
$smarty->assign("typeHospi", $typeHospi);
$smarty->assign("listAff"  , $listAff);
$smarty->assign("crList"   , $crList);
$smarty->assign("hospiList", $hospiList);

$smarty->display("inc_vw_hospi.tpl");

?>