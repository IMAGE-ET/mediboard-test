<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$no_poso = mbGetValueFromGet("no_poso");

$code_cip = mbGetValueFromGet("code_cip");
$prescription_line_id = mbGetValueFromGet("prescription_line_id");

// Chargement de la ligne de prescription
$prescription_line = new CPrescriptionLineMedicament();
$prescription_line->load($prescription_line_id);

// Chargement des moments unitaires
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Chargement de la posologie selectionnée
$posologie = new CBcbPosologie();
$posologie->load($code_cip, $no_poso);

// Ajout de /kg 
$unite_prise_posologie = $posologie->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
if($posologie->p_kg) {
  $unite_prise_posologie .= "/kg";
}
      
      
// Sauvegarde des prises
if($posologie->code_moment && $code_cip && $no_poso){
	$moment = new CBcbMoment();
	$moment->load($posologie->code_moment);
	$moment->loadRefsAssociations();
	foreach($moment->_ref_associations as &$_association){
		$prise_posologie = new CPrisePosologie();
		$prise_posologie->object_id = $prescription_line_id;
		$prise_posologie->object_class = "CPrescriptionLineMedicament";
		$prise_posologie->moment_unitaire_id = $_association->moment_unitaire_id;
		// Si association ne OR, quantite à 0
		if($_association->OR){
			$prise_posologie->quantite = 0;
		} else {
		  $prise_posologie->quantite = $posologie->quantite1;
		}
		$prise_posologie->unite_prise = $unite_prise_posologie;
		if($msg = $prise_posologie->store()){
			return $msg;
		}
	}
} else {
	if($no_poso){
		// Posologie sans moment
	  $prise_posologie = new CPrisePosologie();
		$prise_posologie->object_id = $prescription_line_id;
		$prise_posologie->object_class = "CPrescriptionLineMedicament";
		$prise_posologie->quantite = $posologie->quantite1;
		
		
		// Cas: x fois par y
		if($posologie->code_moment == 0 && $posologie->tous_les <= 1){
		  if(!$prise_posologie->nb_fois && $posologie->_code_duree1 == "jour" && $posologie->quantite1 <= 6){
		    // Traitement specifique pour les moments de type 4 gelules 1 fois par jour => 1 gelule 4 fois par jour
        $prise_posologie->nb_fois = $posologie->quantite1;
        $prise_posologie->unite_fois = $posologie->_code_duree1;
        $prise_posologie->quantite = 1;
		  } else {
		    // Traitement normal
			  if(!$posologie->combien1){
					$prise_posologie->nb_fois = 1;
				} else {
				  $prise_posologie->nb_fois = $posologie->combien1;
				}
				$prise_posologie->unite_fois = $posologie->_code_duree1;
		  }
		} 
		
		else {
	   	// Cas: tous les x y
		  if($posologie->tous_les > 1){
			  $prise_posologie->nb_tous_les = $posologie->tous_les;
		  	$prise_posologie->unite_tous_les = $posologie->_code_duree1;
		  }
		}
		// Stockage de la prise
		$prise_posologie->unite_prise = $unite_prise_posologie;
		if($msg = $prise_posologie->store()){
		 	return $msg;
		}
	}
}

$prescription_line->loadRefsPrises();

if($posologie->pendant1){
	$prescription_line->duree = $posologie->pendant1;
}
if($posologie->_code_duree2){
  $prescription_line->unite_duree = $posologie->_code_duree2;
}
if(!$prescription_line->debut){
  $prescription_line->debut = mbDate();
}

$prescription_line->countPrisesLine();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type", "Med");
$smarty->assign("line", $prescription_line);
$smarty->assign("moments", $moments);
$smarty->assign("typeDate","Med");
$smarty->display("../../dPprescription/templates/line/inc_vw_prises_posologie.tpl");

?>