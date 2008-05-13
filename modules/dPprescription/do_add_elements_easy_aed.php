<?php


function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}


global $AppUI;

$category_id = mbGetValueFromPost("category_id");
$category_name = mbGetValueFromPost("category_name");
$prescription_id = mbGetValueFromPost("prescription_id");

$debut = mbGetValueFromPost("debut");
$duree = mbGetValueFromPost("duree");
$unite_duree = mbGetValueFromPost("unite_duree");

$quantite = mbGetValueFromPost("quantite");
$nb_fois = mbGetValueFromPost("nb_fois");
$unite_fois = mbGetValueFromPost("unite_fois");
$moment_unitaire_id = mbGetValueFromPost("moment_unitaire_id");
$nb_tous_les = mbGetValueFromPost("nb_tous_les");
$unite_tous_les = mbGetValueFromPost("unite_tous_les");

// Chargement de la categorie
$category_prescription = new CCategoryPrescription();
$category_prescription->load($category_id);

// Chargement des elements de la categorie selectionnée
$category_prescription->loadElementsPrescription();

foreach($category_prescription->_ref_elements_prescription as $_element){
	$prescription_line_element = new CPrescriptionLineElement();
	$prescription_line_element->element_prescription_id = $_element->_id;
	$prescription_line_element->prescription_id = $prescription_id;
	$prescription_line_element->praticien_id = $AppUI->user_id;
	$prescription_line_element->debut = $debut;
	$prescription_line_element->duree = $duree;
	$prescription_line_element->unite_duree = $unite_duree;
	$msg = $prescription_line_element->store();
  viewMsg($msg, "msg-CPrescriptionLineElement-create");
  
	$prise = new CPrisePosologie();
	$prise->object_id = $prescription_line_element->_id;
	$prise->object_class = "CPrescriptionLineElement";	
  
	// Prise Moment
	if($quantite && $moment_unitaire_id){
	  $prise->quantite = $quantite;
	  $prise->moment_unitaire_id = $moment_unitaire_id;
	  $msg = $prise->store();
	  viewMsg($msg, "msg-CPrisePosologie-create");
	}
	
	// Prise Fois Par
  if($quantite && $nb_fois && $unite_fois){
	  $prise->quantite = $quantite;
	  $prise->nb_fois = $nb_fois;
	  $prise->unite_fois = $unite_fois;
	  $msg = $prise->store(); 
 	  viewMsg($msg, "msg-CPrisePosologie-create");
  }
	
  // Prise Tous Les
  if($quantite && $nb_tous_les && $unite_tous_les){
	  $prise->quantite = $quantite;
	  $prise->nb_tous_les = $nb_tous_les;
	  $prise->unite_tous_les = $unite_tous_les;
	  $msg = $prise->store();  	
    viewMsg($msg, "msg-CPrisePosologie-create");
  }
	
}

echo "<script type='text/javascript'>window.opener.Prescription.reload($prescription_id,'','$category_name')</script>";
echo $AppUI->getMsg();
exit();

?>