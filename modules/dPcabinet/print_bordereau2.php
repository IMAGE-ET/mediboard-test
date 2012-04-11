<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 12302 $
* @author Alexis Granger
*/

$date = mbDate();

// Dates selectionnees
$date_min = CValue::getOrSession("_date_min", mbDate());
$date_max = CValue::getOrSession("_date_max", mbDate());

// Id du praticien selectionné
$prat = CValue::getOrSession("chir");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($prat);
$praticien->loadRefBanque();

// Extraction des elements qui composent le numero de compte
$compte_banque  = substr($praticien->compte, 0, 5);
$compte_guichet = substr($praticien->compte, 5, 5);
$compte_numero  = substr($praticien->compte, 10, 11);
$compte_cle     = substr($praticien->compte, 21, 2);

// Nombre de cheques remis
$nbRemise = 0;

// Montant total des cheques
$montantTotal = 0;

$where = array();
$where['reglement.mode']     = "= 'cheque' ";
$where['reglement.date']     = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59' ";
$where['reglement.object_class']     = " = 'CConsultation'";

$ljoin = array();
$ljoin['consultation']       = "consultation.consultation_id = reglement.object_id";

if ($praticien->_id) {
  $where['plageconsult.chir_id'] = "= '$praticien->_id'";
  $ljoin['plageconsult']         = "plageconsult.plageconsult_id = consultation.plageconsult_id";
}

$orderby = "reglement.date ASC";
// Recherche des reglements
$reglement = new CReglement();
$list_reglements = $reglement->loadList($where, $orderby, null, null, $ljoin);

$where['reglement.object_class']     = " = 'CFactureConsult'";

unset($where['plageconsult.chir_id']);
$ljoin = array();
$ljoin['factureconsult']       = "factureconsult.factureconsult_id = reglement.object_id";

$supplements = $reglement->loadList($where, $orderby, null, null, $ljoin);
foreach($supplements as $key => $reglement){
	$list_reglements[$key] = $reglement;
}

// Chargements des consultations
foreach($list_reglements as $curr_reglement){
  $curr_reglement->loadRefsFwd();
  
  $curr_consult = $curr_reglement->_ref_object;
	$curr_consult->loadRefPraticien();
	$curr_consult->loadRefPatient();
	
	$montantTotal += $curr_reglement->montant;
}
$nbRemise = count($list_reglements);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"            , $praticien      );
$smarty->assign("list_reglements"      , $list_reglements);
$smarty->assign("date"                 , $date           );
$smarty->assign("compte_banque"        , $compte_banque  );
$smarty->assign("compte_guichet"       , $compte_guichet );
$smarty->assign("compte_numero"        , $compte_numero  );
$smarty->assign("compte_cle"           , $compte_cle     );
$smarty->assign("montantTotal"         , $montantTotal   );
$smarty->assign("nbRemise"             , $nbRemise       );
$smarty->display("print_bordereau2.tpl");

?>