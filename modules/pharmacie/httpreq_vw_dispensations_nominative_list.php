<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id =      mbGetValueFromGetOrSession('service_id');
$patient_id =      mbGetValueFromGetOrSession('patient_id');
$prescription_id = mbGetValueFromGetOrSession('prescription_id');
$_code_cip       = mbGetValueFromGet("code_cip");

$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');

$date_min_orig = $date_min;
$date_max_orig = $date_max;

$lines = array();

// Creation du tableau de dates
$dates = array();
if($date_min != $date_max){
	$date = $date_min;
	while($date <= $date_max){
	  $dates[] = $date;
	  $date = mbDate("+ 1 DAY", $date);
	}
} else {
  $dates[] = $date_min; 
}

$date_min .= " 00:00:00";
$date_max .= " 23:59:59";

if($prescription_id == "undefined"){
	$prescription_id = "";
}

$prescription = new CPrescription();
$dispensations = array();
$delivrances = array();
$prescriptions = array();
$stocks = array();
$done = array();
$done_global = array();
$stocks_service = array();
$warning = array();
$produits = array();

$prescription = new CPrescription();
$prescription->load($prescription_id);

if($prescription->_id){
 
  // Stockage du sejour de la prescription
  $sejour =& $prescription->_ref_object;
   
  if(!$sejour->_ref_patient){
    $sejour->loadRefPatient();
   }
   $patient =& $sejour->_ref_patient;

   // On borne les dates aux dates du sejour si besoin
  $date_min = max($sejour->_entree, $date_min);
  $date_max = min($sejour->_sortie, $date_max);

  // Chargement des lignes
  $prescription->loadRefsLinesMed("1","1");
  $prescription->loadRefsPerfusions();
  
  $lines_med = array();
  $lines_med["medicament"] = $prescription->_ref_prescription_lines;
  
  // Calcul du plan de soin
  foreach($dates as $_date){
    $prescription->calculPlanSoin($_date, 0, 0, 1, $_code_cip);
  }
   
  // Parcours des prises prevues pour les medicaments
  foreach($lines_med as $lines_by_type){
    foreach($lines_by_type as $_line_med){
      if($_code_cip && ($_line_med->code_cip != $_code_cip)){
        continue;
      }
      if($_line_med->_quantity_by_date){
        if(!isset($produits[$_line_med->code_cip])){
          $produits[$_line_med->code_cip] = $_line_med->_ref_produit;
        }
        $lines[$_line_med->code_cip] = $_line_med;
        foreach($_line_med->_quantity_by_date as $type => $quantity_by_date){
			  	foreach($quantity_by_date as $date => $quantity_by_hour){
			  	  if (@$quantity_by_hour['quantites']) { //FIXME: parfois cette valeur est vide
				  	  foreach($quantity_by_hour['quantites'] as $hour => $quantity){
					      @$dispensations[$_line_med->code_cip]["quantite_administration"] += $quantity["total"];
					      @$dispensations[$_line_med->code_cip]["quantite_dispensation"] += $quantity["total_disp"];
				  	  }
			  		}
			  	}
        }
      }	
      // Gestion des prises planifiees
      if($_line_med->_administrations){
	      foreach($_line_med->_administrations as $unite_prise => &$administrations_by_unite){
			    foreach($administrations_by_unite as $_date => &$administrations_by_date){
					  foreach($administrations_by_date as $_hour => &$administrations_by_hour){
						  if(is_numeric($_hour)){
	          		$quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
		            if($quantite_planifiee){
							    // Calcul de la quantite 
							    @$dispensations[$_line_med->code_cip]["quantite_administration"] += $quantite_planifiee;
							    $quantite_dispensation = $quantite_planifiee * $_line_med->_ratio_administration_dispensation; 
							    @$dispensations[$_line_med->code_cip]["quantite_dispensation"] += $quantite_dispensation;
		            }
	            }
	          }
	        }
	      }
      }
    }
  }
  // Parcours des prises prevues pour les perfusions
  if($prescription->_ref_perfusions_for_plan){
	  foreach($prescription->_ref_perfusions_for_plan as $_perfusion){
	    foreach($_perfusion->_prises_prevues as $_date => $_prises_by_hour){
	      foreach($_prises_by_hour as $_hour => $_prise){
	        foreach($_perfusion->_ref_lines as $_perf_line){
	          $datetime = "$_date $_hour:00:00";
	          if ($datetime < $date_min || $datetime > $date_max){
	            continue;
	          }
	          
	          if(!isset($produits[$_perf_line->code_cip])){
	           $produits[$_perf_line->code_cip] = $_perf_line->_ref_produit;
	          }      
	          $lines[$_perf_line->code_cip] = $_perf_line;
	          if(!isset($dispensations[$_perf_line->code_cip])){
	            $dispensations[$_perf_line->code_cip]["quantite_administration"] = 0;
	            $dispensations[$_perf_line->code_cip]["quantite_dispensation"] = 0;
	          }
	          $dispensations[$_perf_line->code_cip]["quantite_administration"] += $_perf_line->_quantite_administration;
	          $dispensations[$_perf_line->code_cip]["quantite_dispensation"] += $_perf_line->_quantite_dispensation;
	        }
	      }
	    }
	  }
  }
}
  
foreach($dispensations as $cip => $quantite){
  if ($dispensations[$cip]["quantite_administration"] == 0) {
    unset($dispensations[$cip]); continue;
  }
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
     $delivrances[$cip]->_ref_stock->loadRefsFwd();
     $delivrances[$cip]->_ref_stock->_ref_product->updateFormFields();
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
  $where['product_delivery.date_dispensation'] = "BETWEEN '$date_min_orig 00:00:00' AND '$date_max_orig 23:59:59'"; // entre les deux dates
  $where['product.code'] = "= '$code_cip'"; // avec le bon code CIP et seulement les produits du livret thérapeutique
  $where['product.category_id'] = '= '.CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id');
  //$where['product_delivery.patient_id'] = "IS NOT NULL";
  $where['product_delivery.patient_id'] = "= '$patient_id'";
  $where['product_delivery.quantity'] = " > 0";
  // Pour faire le lien entre le produit et la delivrance, on utilise le stock etablissement
  $ljoin = array();
  $ljoin['product_stock_group'] = 'product_delivery.stock_id = product_stock_group.stock_id';
  $ljoin['product'] = 'product_stock_group.product_id = product.product_id';
  
  $deliv = new CProductDelivery();
  $list_done = $deliv->loadList($where, null, null, null, $ljoin);

  $done[$code_cip] = array();
  $done_global[$code_cip] = array();
  
  if (count($list_done)) {
    $done[$code_cip][0] = 0;
 	  foreach ($list_done as $d) {
 	  	$d->loadRefsBack();
 	    $done[$code_cip][] = $d;
 	    $done[$code_cip][0] += $d->quantity;
 	  }
  }
  
  $where['product_delivery.patient_id'] = "IS NULL";
  $list_done_global = $deliv->loadList($where, null, null, null, $ljoin);
  
	if (count($list_done_global)) {
    $done_global[$code_cip][0] = 0;
 	  foreach ($list_done_global as $d_glob) {
 	  	$d_glob->loadRefsBack();
 	    $done_global[$code_cip][] = $d_glob;
 	    $done_global[$code_cip][0] += $d_glob->quantity;
 	  }
  }
  if(isset($delivrances[$code_cip])) {
    $delivrances[$code_cip]->quantity = max($_quantites["quantite_dispensation"] - (isset($done[$code_cip][0]) ? $done[$code_cip][0] : 0), 0);
  }
  $stocks_service[$code_cip] = CProductStockService::getFromCode($code_cip, $service_id);
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("produits"           , $produits);
$smarty->assign("lines"              , $lines);
$smarty->assign('delivrances'        , $delivrances);
$smarty->assign('done'               , $done);
$smarty->assign('done_global'        , $done_global);
$smarty->assign('stocks_service'     , $stocks_service);
$smarty->assign('service_id'         , $service_id);
$smarty->assign('prescription'       , $prescription);
$smarty->assign('mode_nominatif'     , "1");
$smarty->assign("date_min", mbDate(mbGetValueFromGetOrSession('_date_min')));
$smarty->assign("date_max", mbDate(mbGetValueFromGetOrSession('_date_max')));
$smarty->assign("now", mbDate());

if($_code_cip){
  $smarty->assign("quantites", $dispensations[$_code_cip]);
  $smarty->assign("code_cip", $_code_cip);
  $smarty->assign("nodebug", true);
  $smarty->display('inc_dispensation_line.tpl');
} else {
  $smarty->assign('dispensations'      , $dispensations);
  $smarty->display('inc_dispensations_list.tpl');
}

?>