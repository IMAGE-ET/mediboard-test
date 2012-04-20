<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

if ($praticien_id = CValue::post("praticien_id")) {
  CValue::setSession("praticien_id", $praticien_id);
}
//Pour un séjour ayant comme mode de sortie urgence:
if(CValue::post("mode_sortie") == "mutation" && CValue::post("type") == "urg" && CValue::post("lit_id")){
	
	$sejour_id = CValue::post("sejour_id");
	$lit_id = CValue::post("lit_id");
  $sejour = new CSejour();
  $sejour->load($sejour_id);
	
  //Création de l'affectation du patient 
  $affectation = new CAffectation();
  $affectation->entree = mbDateTime();
  $affectation->lit_id = $lit_id;
  $affectation->sejour_id = $sejour_id;
  $affectation->sortie = $sejour->sortie_prevue;
  $affectation->store();
  
  //Suppression du blocage d'urgence si l'affectation couvre le temps de celui-ci
  $affectation_urgence =  new CAffectation();
  $where = array();
  $where["sortie"] = " BETWEEN '$affectation->entree' AND '$affectation->sortie'";
  $where["lit_id"] = "= '$lit_id'";
  $where["function_id"] = "IS NOT NULL";
  
  if($affectation_urgence->loadObject($where)){
  	$affectation_urgence->delete();
  }
  //Réduction du blocage d'urgence sinon
  else{
    $where = array();
  	$where[] = "entree <= '$affectation->entree' OR entree <= '$affectation->sortie'";
    $where[] = "sortie >= '$affectation->sortie' OR sortie BETWEEN '$affectation->entree' AND '$affectation->sortie'";
	  $where["lit_id"] = "= '$lit_id'";
	  $where["function_id"] = "IS NOT NULL";
  	$affectation_urgence->loadObject($where);
  	
  	$affectation_urgence->entree = $affectation->sortie;
  	$affectation_urgence->sejour_id = null;
  	$msg = $affectation_urgence->store();
  }
}

$do = new CDoObjectAddEdit("CSejour", "sejour_id");
$do->doIt();

?>