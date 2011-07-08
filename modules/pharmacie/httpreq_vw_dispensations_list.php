<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$service_id      = CValue::getOrSession('service_id');
$_selected_cis   = CValue::get("_selected_cis");
$prescription_id = CValue::get("prescription_id");

$service = new CService();
$service->load($service_id);

if($prescription_id == "undefined"){
  $prescription_id = "";
}

$datetime_min = CValue::getOrSession('_datetime_min');
$datetime_max = CValue::getOrSession('_datetime_max');

CPrescriptionLineMedicament::$_load_lite = true;
CPrescriptionLineMixItem::$_load_lite = true;

$prescription = new CPrescription();
$nb_done_total = array();
$prises = array();
$default_prises = array();

// Nominatif
if($prescription_id){
	$mode_nominatif = 1;
	
	$prescription->load($prescription_id);
	$prescriptions[$prescription_id] = $prescription;
	$patient_id = $prescription->_ref_object->patient_id;
} 
// Nominatif reglobalisé
else {
	$mode_nominatif = 0;
	
	$where = array();
	$ljoin = array();
	$ljoin['sejour'] = 'prescription.object_id = sejour.sejour_id';
	$ljoin['affectation'] = 'sejour.sejour_id = affectation.sejour_id';
	$ljoin['lit'] = 'affectation.lit_id = lit.lit_id';
	$ljoin['chambre'] = 'lit.chambre_id = chambre.chambre_id';
	$ljoin['service'] = 'chambre.service_id = service.service_id';
	$where['prescription.type'] = " = 'sejour'";
	$where[] = "sejour.entree <= '$datetime_max'";
	$where[] = "sejour.sortie >= '$datetime_min'";		
	$where['service.service_id'] = " = '$service_id'";
	$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
}

$dispensations = array();
$delivrances = array();
$produits_cis = array();
$produits_cip = array();

$stocks = array();
$quantites_reference = array();
$quantites = array();
$done = array();
$besoin_patients = array();
$stocks_service = array();
$stocks_pharmacie = array();
$besoin_patient = array();
$done_delivery = array();
$_lines = array();

$_lines = array();
$correction_dispensation = array();

if($prescriptions) {
  foreach($prescriptions as $_prescription) {
    // Stockage du sejour de la prescription
    $sejour =& $_prescription->_ref_object;
    $sejour->loadRefPatient();
    $patient =& $sejour->_ref_patient;
    
    // Calcul des planifs systemes si elles ne sont pas deja calculées
    $_prescription->calculAllPlanifSysteme();
    
    // Chargement des planifs systemes
    $planif = new CPlanificationSysteme();
    $where = array();
    $where["sejour_id"] = " = '$_prescription->object_id'";
    $where[] = "object_class = 'CPrescriptionLineMedicament' OR object_class = 'CPrescriptionLineMixItem'";
    $where["dateTime"] = " BETWEEN '$datetime_min' AND '$datetime_max'";
		
		$ljoin = array();
		if ($_selected_cis) {
			$ljoin["prescription_line_medicament"] = "planification_systeme.object_id = prescription_line_medicament.prescription_line_medicament_id 
			                                          AND planification_systeme.object_class = 'CPrescriptionLineMedicament'";
																								
			$ljoin["prescription_line_mix_item"] = "planification_systeme.object_id = prescription_line_mix_item.prescription_line_mix_item_id 
                                                AND planification_systeme.object_class = 'CPrescriptionLineMixItem'";
      
			$where[] = "prescription_line_medicament.code_cis = '$_selected_cis' OR prescription_line_mix_item.code_cis = '$_selected_cis'";
		}
		
    $planifs = $planif->loadList($where, "dateTime ASC", null, null, $ljoin);

    foreach($planifs as $_planif){
      // Chargement et stockage de la ligne
      $_planif->loadTargetObject();
  
      if($_planif->_ref_object instanceof CPrescriptionLineMedicament){
      	// Chargement de la prise
        $_planif->loadRefPrise();
        $qte_adm = $_planif->_ref_prise->_quantite_administrable ? $_planif->_ref_prise->_quantite_administrable : 1; 
        $qte_disp = $_planif->_ref_prise->_quantite_dispensation;
      }
      
      if($_planif->_ref_object instanceof CPrescriptionLineMixItem){
				$_planif->_ref_object->updateQuantiteAdministration();
        $qte_adm = $_planif->_ref_object->_quantite_administration;
        $qte_disp = $_planif->_ref_object->_quantite_dispensation;
      }
      
      $code = $_planif->_ref_object->code_cis ? $_planif->_ref_object->code_cis : $_planif->_ref_object->code_cip;
      if($mode_nominatif){
      	if($_planif->_ref_object instanceof CPrescriptionLineMedicament){
      	  $_planif->_ref_object->loadRefsPrises();
        }
      	$_lines[$code][$_planif->_ref_object->_guid] = $_planif->_ref_object;
      }
			
      if(!isset($produits_cis[$code])){
         $produits_cis[$code] = $_planif->_ref_object;
      } 
      
      if(!isset($besoin_patient[$code][$patient->_id])){
        $besoins =& $besoin_patient[$code][$patient->_id];
        $besoins = array("patient" => "", "quantite_administration" => 0, "quantite_dispensation" => 0);
      }
      
      if(!isset($dispensations[$code])){
        $dispensations[$code]["quantite_administration"] = 0;
        $dispensations[$code]["quantite_dispensation"] = 0;
      }
      $dispensations[$code]["quantite_administration"] += $qte_adm;
      $dispensations[$code]["quantite_dispensation"] += $qte_disp;
      
      $besoin_patient[$code][$patient->_id]["patient"] = $patient; 
      $besoin_patient[$code][$patient->_id]["quantite_administration"] += $qte_adm;
      $besoin_patient[$code][$patient->_id]["quantite_dispensation"] += $qte_disp;
			
			if ($mode_nominatif) {
				$default_prises[$code][$_planif->_id]["datetime"] = $_planif->dateTime;
        $default_prises[$code][$_planif->_id]["quantite_adm"] = $qte_adm;
				$default_prises[$code][$_planif->_id]["unite_adm"] = utf8_encode($_planif->_ref_object->_unite_administration);
        $default_prises[$code][$_planif->_id]["object_id"] = $_planif->_ref_object->_id;
        $default_prises[$code][$_planif->_id]["object_class"] = $_planif->_ref_object->_class_name;
      } 
    }
  }
}  

foreach($besoin_patient as &$quantites_by_patient){
  foreach($quantites_by_patient as &$quantites){
    if(strstr($quantites["quantite_dispensation"],'.')){
      $quantites["quantite_dispensation"] = ceil($quantites["quantite_dispensation"]);
    }
  }
}

$category_id = CAppUI::conf('bcb CBcbProduitLivretTherapeutique product_category_id');

// Calcul du nombre de boites (unites de presentation)
foreach($dispensations as $code => $unites){
  if ($dispensations[$code]["quantite_administration"] == 0) {
    unset($dispensations[$code]); continue;
  }
  
  if(strstr($dispensations[$code]["quantite_dispensation"],'.')){
    $dispensations[$code]["quantite_dispensation"] = ceil($dispensations[$code]["quantite_dispensation"]);
  }
    
  // Si le code est bien un code CIS
  if(strlen($code) == '8'){
    $_produits_from_cis[$code] = CBcbProduit::getProduitsFromCISInLivret($code); 
  } else {
    $produit = CBcbProduit::get($code);
    $_produits_from_cis[$code] = array(array("CODE_CIP" => $code, "LIBELLE_PRODUIT" => $produit->libelle));
  }
  
  foreach($_produits_from_cis[$code] as $_produit){
    $_code_cip = $_produit["CODE_CIP"];
    $produits_cip[$_code_cip] = $_produit;
    
    $product = new CProduct();
    $product->code = $_code_cip;
    $product->category_id = $category_id;
    
    if ($product->loadMatchingObject()) {
      // Chargement du stock
      $product->loadRefStock();
      $stock = $product->_ref_stock_group;
      
      // Chargement des ref de la dispensation à effectuer
      $delivery = new CProductDelivery();
      $delivery->stock_id = $stock->_id;
      $delivery->stock_class = $stock->_class_name;
      $delivery->service_id = $service_id;
      $delivery->loadRefsFwd();
      $delivery->_ref_stock->loadRefsFwd();
      $delivery->_ref_stock->_ref_product->updateFormFields();
      $delivrances[$code][$_code_cip] = $delivery;
      
      // Calcul du stock de la pharmacie
      $stocks_pharmacie[$code][$_code_cip] = $delivery->_ref_stock->quantity;
      if(!isset($stocks_pharmacie[$code]["total"])){
        $stocks_pharmacie[$code]["total"] = 0;
      }
      $stocks_pharmacie[$code]["total"] += $stocks_pharmacie[$code][$_code_cip];
    }
  }
}

// On arrondit la quantite de "boites"
foreach($dispensations as $code_cis => $_quantites){
	$nb_done_total[$code_cis]=0;
	
  $_produits = $_produits_from_cis[$code_cis];
  foreach($_produits as $_produit){
    $code_cip = $_produit["CODE_CIP"];
    
    // Chargement des dispensation déjé effectuée
    $where = array();
    //$where['product_delivery.date_dispensation'] = "BETWEEN '$datetime_min' AND '$datetime_max'"; // entre les deux dates
    
    $where['product_delivery.datetime_min'] = " < '$datetime_max'";
    $where['product_delivery.datetime_max'] = " > '$datetime_min'";
		
		$where['product.code'] = "= '$code_cip'"; // avec le bon code CIP et seulement les produits du livret thérapeutique
    $where['product.category_id'] = "= '$category_id'";
		if($mode_nominatif){
			$where['product_delivery.patient_id'] = " = '$patient_id'";
		} else {
			$where['product_delivery.patient_id'] = "IS NULL";
		}
    $where['product_delivery.quantity'] = " > 0";
    $where['product_delivery.stock_class'] = "= 'CProductStockGroup'";
    
    // Pour faire le lien entre le produit et la delivrance, on utilise le stock etablissement
    $ljoin = array();
    $ljoin['product_stock_group'] = 'product_delivery.stock_id = product_stock_group.stock_id';
    $ljoin['product'] = 'product_stock_group.product_id = product.product_id';
    
    $deliv = new CProductDelivery();
    $list_done = $deliv->loadList($where, null, null, null, $ljoin);

    $done[$code_cis][$code_cip] = array();
    $done_delivery[$code_cis][$code_cip] = array();
    
    if (count($list_done)) {
      if(!isset($done[$code_cis]["total"])){
        $done[$code_cis]["total"] = 0;
      }
      foreach ($list_done as $d) {
        $d->loadRefsBack();
        $done[$code_cis][$code_cip][] = $d;
        $done[$code_cis]["total"] += $d->quantity;
				$nb_done_total[$code_cis]++;
      }
    }
  
    // Chargement des dispensation déjà effectuée (dans l'autre mode)
    if($mode_nominatif){
      $where['product_delivery.patient_id'] = "IS NULL";
    } else {
      $where['product_delivery.patient_id'] = "IS NOT NULL";
    }
    $list_done_delivery = $deliv->loadList($where, null, null, null, $ljoin);
    
    if (count($list_done_delivery)) {
      if(!isset($done_delivery[$code_cis]["total"])){
        $done_delivery[$code_cis]["total"] = 0;
      }
      foreach ($list_done_delivery as $_d_delivery) {
        $_d_delivery->loadRefsBack();
        $_d_delivery->loadRefPatient();
        $done_delivery[$code_cis][$code_cip][] = $_d_delivery;
        $done_delivery[$code_cis]["total"] += $_d_delivery->quantity;
				$nb_done_total[$code_cis]++;
      }
    }
    $stocks_service[$code_cis][$code_cip] = CProductStockService::getFromCode($code_cip, $service_id);
    if(!isset($stocks_service[$code_cis]["total"])){
      $stocks_service[$code_cis]["total"] = 0;
    }
    $stocks_service[$code_cis]["total"] += $stocks_service[$code_cis][$code_cip]->quantity;
  }
}

// Ajustement de la valeur proposé dans la dispensation en fonction de celles deja effectuées
foreach($done as $_done_cis => $_done) {
  if(isset($delivrances[$_done_cis])){
    foreach($delivrances[$_done_cis] as $_delivery_cip => $_delivery){
      $_quantites = $dispensations[$_done_cis];     
      $_delivery->quantity = max($_quantites["quantite_dispensation"] - (isset($_done["total"]) ? $_done["total"] : 0), 0);
      if(!CAppUI::conf("dPstock CProductStockGroup infinite_quantity")){
        $_delivery->quantity = min($_delivery->quantity, $stocks_pharmacie[$_done_cis][$_delivery_cip]);
      }
    }
  }
}

// Patch unite de dispensation
foreach($delivrances as $code_cis => $_delivrance){
  $produits = CBcbProduit::getProduitsFromCIS($code_cis);
  foreach($produits as $_produit_bcb){
    if(!array_key_exists($_produit_bcb["CODE_CIP"], $delivrances[$code_cis])){
      continue;
    }
    $produit = CBcbProduit::get($_produit_bcb["CODE_CIP"]);
    $produit->loadConditionnement();
    $produit->loadLibellePresentation();
    $produit->_unite_administration = $produit->libelle_unite_presentation;
    $produit->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;
    
    if($produit->_unite_dispensation == $produit->libelle_unite_presentation){
      $ratio = 1;
    } else {
      $ratio = 1 / $produit->nb_unite_presentation;
    }
		
		if(isset($default_prises[$code_cis])){
			foreach($default_prises[$code_cis] as $_planif_id => $_prise_planif){
				foreach($_prise_planif as $_key_planif => $_value_planif){
					$prises[$code_cis][$produit->code_cip][$_planif_id][$_key_planif] = $_value_planif;
		    }
				$_planif_quantite_disp = $prises[$code_cis][$produit->code_cip][$_planif_id]["quantite_adm"] * $ratio;
				$prises[$code_cis][$produit->code_cip][$_planif_id]["quantite_disp"] = ceil($_planif_quantite_disp);
			}
		}
		
    // Calcul de la quantite 
    $administration = $dispensations[$code_cis]["quantite_administration"];
    $quantite_dispensation = $ratio*$administration;
    if(strstr($quantite_dispensation,'.')){
      $quantite_dispensation = ceil($quantite_dispensation);
    }
    
    $correction_dispensation[$code_cis][$produit->code_cip]["produit"] = $produit->libelle;
    $correction_dispensation[$code_cis][$produit->code_cip]["dispensation"] = $quantite_dispensation;
    $correction_dispensation[$code_cis]["nb"]["$quantite_dispensation"] = $quantite_dispensation;
  }
}

foreach($correction_dispensation as $code_cis => $_correction){
  if(count($correction_dispensation[$code_cis]["nb"]) ==  1){
    unset($correction_dispensation[$code_cis]);
    continue;
  }  
  foreach($delivrances[$code_cis] as $code_cip => $_delivery){
    $_delivery->quantity = $correction_dispensation[$code_cis][$code_cip]["dispensation"];
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("correction_dispensation", $correction_dispensation);
$smarty->assign("besoin_patient", $besoin_patient);
$smarty->assign('delivrances', $delivrances);
$smarty->assign('produits_cip'  , $produits_cip);
$smarty->assign('produits_cis'  , $produits_cis);
$smarty->assign('done'  , $done);
$smarty->assign('done_delivery', $done_delivery);
$smarty->assign('stocks_service'  , $stocks_service);
$smarty->assign('stocks_pharmacie'  , $stocks_pharmacie);
$smarty->assign('service', $service);
$smarty->assign("datetime_min", $datetime_min);
$smarty->assign("datetime_max", $datetime_max);
$smarty->assign("now", mbDate());
$smarty->assign('mode_nominatif', $mode_nominatif);
$smarty->assign("_lines", $_lines);
$smarty->assign("nb_done_total", $nb_done_total);
$smarty->assign("prises", $prises);

if($mode_nominatif){
  $prescription->_ref_object->loadRefPatient();
  $smarty->assign('prescription', $prescription);
}
	
if($_selected_cis){
  // Refresh d'une ligne
  $smarty->assign("quantites", $dispensations[$_selected_cis]);
  $smarty->assign("code_cis", $_selected_cis);
  $smarty->assign("nodebug", true);
  $smarty->display('inc_dispensation_line.tpl');
} 
else {
  $smarty->assign('dispensations', $dispensations);
  $smarty->display('inc_dispensations_list.tpl');
}
	
?>