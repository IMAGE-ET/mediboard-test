<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author Alexis Granger
 */

$service_id =      mbGetValueFromGetOrSession('service_id');
$patient_id =      mbGetValueFromGetOrSession('patient_id');
$prescription_id = mbGetValueFromGetOrSession('prescription_id');

$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');

if($prescription_id == "undefined"){
	$prescription_id = "";
}

$prescription = new CPrescription();
$dispensations = array();
$delivrances = array();
$prescriptions = array();
$stocks = array();
$done = array();
$stocks_service = array();
$warning = array();
$produits = array();

if($prescription_id){
	$prescription = new CPrescription();
	$prescription->load($prescription_id);
	
  // Stockage du sejour de la prescription
  $sejour =& $prescription->_ref_object;
  if(!$sejour->_ref_patient){
  	$sejour->loadRefPatient();
  }
  $patient =& $sejour->_ref_patient;
  
  // On borne les dates aux dates du sejour si besoin
  $date_min = max($sejour->_entree, $date_min);
  $date_max = min($sejour->_sortie, $date_max);
  
  //if ($date_min > $date_max) continue;
  $prescription->loadRefsLinesMed(1,1);
  foreach($prescription->_ref_prescription_lines as $_line_med){ 
    if (!$_line_med->debut) continue;
    
    $_line_med->_ref_produit->loadConditionnement();
    
    // On remplit les bornes de la ligne avec les dates du sejour si besoin
    $_line_med->_debut_reel = (!$_line_med->_debut_reel) ? $sejour->_entree : $_line_med->_debut_reel;
    $_line_med->_fin_reelle = (!$_line_med->_fin_reelle) ? $sejour->_sortie : $_line_med->_fin_reelle;
    
    // Si la ligne n'est pas dans les bornes donné, on en tient pas compte
    if (!($_line_med->_debut_reel >= $date_min && $_line_med->_debut_reel <= $date_max ||
        $_line_med->_fin_reelle >= $date_min && $_line_med->_fin_reelle <= $date_max ||
        $_line_med->_debut_reel <= $date_min && $_line_med->_fin_reelle >= $date_max)){
      continue;     
    }
    
    // Calcul de la quantite totale de la ligne en fonction des prises dans les dates indiquées
    $_line_med->calculQuantiteLine($date_min, $date_max);
    
    if($_line_med->_quantite_administration){
      $_line_med->_quantite_dispensation = $_line_med->_quantite_administration * $_line_med->_ratio_administration_dispensation;
      if(!isset($dispensations[$_line_med->code_cip]["quantite_administration"])){
        $dispensations[$_line_med->code_cip]["quantite_administration"] = 0;
      }  
      if(!isset($dispensations[$_line_med->code_cip]["quantite_dispensation"])){
        $dispensations[$_line_med->code_cip]["quantite_dispensation"] = 0;
      }     
      $dispensations[$_line_med->code_cip]["quantite_administration"] += $_line_med->_quantite_administration;
      $dispensations[$_line_med->code_cip]["quantite_dispensation"] += $_line_med->_quantite_dispensation;
    }
    $produits[$_line_med->code_cip] = $_line_med->_ref_produit;
  }
  
  // Chargement des perfusions
  $prescription->loadRefsPerfusions();
  
  foreach($prescription->_ref_perfusions as $_perfusion){
    if (!(($_perfusion->_debut >= $date_min) && ($_perfusion->_debut <= $date_max))){
     continue;     
    }
    
    $_perfusion->loadRefsLines();
    foreach($_perfusion->_ref_lines as &$_perf_line){
      
      $produit = $_perf_line->_ref_produit;
      $produit->loadLibellePresentation();
      
      $poids_ok = 1;
      $_unite_prise = str_replace('/kg','',$_perf_line->unite);
      if($_unite_prise != $_perf_line->unite){
        if(!$patient->_ref_constantes_medicales){
	        $patient->loadRefConstantesMedicales();
	      }
        $poids = $patient->_ref_constantes_medicales->poids;
        if($poids){
		      $_perf_line->quantite *= $poids;
		      $_perf_line->_unite_sans_kg = $_unite_prise;
        } else {
          $poids_ok = 0;
          $_perf_line->quantite = 0;
        } 
      }
      
      if($poids_ok){
        $unite_prise = ($_perf_line->_unite_sans_kg) ? $_perf_line->_unite_sans_kg : $_perf_line->unite;
		    $produit->loadConditionnement();
		    // Gestion des unites de prises exprimées en libelle de presentation (ex: poche ...)		    
		    if($_perf_line->unite == $produit->libelle_presentation){		        
		      $_perf_line->quantite *= $produit->nb_unite_presentation;
		    }
		    // Gestion des unite autres unite de prescription
		    if(!isset($produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation])) {
          $coef = 1;
        } else {
          $coef = $produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation];
        }
        
        $_perf_line->_quantite_with_coef = 1;
		    $_perf_line->quantite *= $coef;
		    
		    $_perf_line->_unite_administration = $produit->libelle_unite_presentation;
		    $_perf_line->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;
		    $produit->_unite_dispensation = $_perf_line->_unite_dispensation;
		    $produit->_unite_administration = $_perf_line->_unite_administration;
		    
		    if($_perf_line->_unite_dispensation == $produit->libelle_unite_presentation){
		      $_perf_line->_ratio_administration_dispensation = 1;
		    } else {
		      $_perf_line->_ratio_administration_dispensation = 1 / $produit->nb_unite_presentation;
		    }
		  }
		  @$_perf_line->_quantite_administration += $_perf_line->quantite; 

		  if($_perf_line->_quantite_administration){
	      $_perf_line->_quantite_dispensation = $_perf_line->_quantite_administration * $_perf_line->_ratio_administration_dispensation;
	      if(!isset($dispensations[$_perf_line->code_cip]["quantite_administration"])){
	        $dispensations[$_perf_line->code_cip]["quantite_administration"] = 0;
	      }  
	      if(!isset($dispensations[$_perf_line->code_cip]["quantite_dispensation"])){
	        $dispensations[$_perf_line->code_cip]["quantite_dispensation"] = 0;
	      }     
	      $dispensations[$_perf_line->code_cip]["quantite_administration"] += $_perf_line->_quantite_administration;
	      $dispensations[$_perf_line->code_cip]["quantite_dispensation"] += $_perf_line->_quantite_dispensation;
      }
      $produits[$_perf_line->code_cip] = $_perf_line->_ref_produit;   
    } 
  }

	foreach($dispensations as $cip => $quantite){
	  $product = new CProduct();
	  $product->code = $cip;
	  $product->category_id = CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id');
	  
	  if ($product->loadMatchingObject()) {
	    global $g;
	    $stocks[$cip] = new CProductStockGroup();
	    $stocks[$cip]->group_id = $g;
	    $stocks[$cip]->product_id = $product->_id;
	    $stocks[$cip]->loadMatchingObject();
	    
	    $delivrances[$cip] = new CProductDelivery();
	    $delivrances[$cip]->stock_id = $stocks[$cip]->_id;
	    $delivrances[$cip]->service_id = $service_id;
	    $delivrances[$cip]->loadRefsFwd();
	  }
	}
	
	foreach($dispensations as $code_cip => &$_quantites){
	  foreach($_quantites as &$_quantite){
	    if(strstr($_quantite,'.')){
	      $_quantite = ceil($_quantite);
	    }
	  }
	  // Chargement des dispensation déjé effectuée
	  $where = array();
	  $where['product_delivery.date_dispensation'] = "BETWEEN '$date_min' AND '$date_max'"; // entre les deux dates
	  $where['product.code'] = "= '$code_cip'"; // avec le bon code CIP et seulement les produits du livret thérapeutique
	  $where['product.category_id'] = '= '.CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id');
	  $where['product_delivery.patient_id'] = "IS NOT NULL";
	  $where['product_delivery.quantity'] = " > 0";
	  // Pour faire le lien entre le produit et la delivrance, on utilise le stock etablissement
	  $ljoin = array();
	  $ljoin['product_stock_group'] = 'product_delivery.stock_id = product_stock_group.stock_id';
	  $ljoin['product'] = 'product_stock_group.product_id = product.product_id';
	  
	  $deliv = new CProductDelivery();
	  $list_done = $deliv->loadList($where, null, null, null, $ljoin);
	  $done[$code_cip] = array();
	  
	  if (count($list_done)) {
	    $done[$code_cip][0] = 0;
  	  foreach ($list_done as $d) {
  	  	$d->loadRefsBack();
  	    $done[$code_cip][] = $d;
  	    $done[$code_cip][0] += $d->quantity;
  	  }
	  }
	  if(isset($delivrances[$code_cip])) {
	    $delivrances[$code_cip]->quantity = max($_quantites["quantite_dispensation"] - (isset($done[$code_cip][0]) ? $done[$code_cip][0] : 0), 0);
	  }
	  $stocks_service[$code_cip] = CProductStockService::getFromCode($code_cip, $service_id);
	}
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("produits"           , $produits);
$smarty->assign('dispensations'      , $dispensations);
$smarty->assign('delivrances'        , $delivrances);
$smarty->assign('done'               , $done);
$smarty->assign('stocks_service'     , $stocks_service);
$smarty->assign('service_id'         , $service_id);
$smarty->assign('prescription'       , $prescription);
$smarty->assign('mode_nominatif'     , "1");
$smarty->display('inc_dispensations_list.tpl');

?>