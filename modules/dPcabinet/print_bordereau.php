<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$date = mbDate();

// Dates selectionnees
$date_min = mbGetValueFromGetOrSession("_date_min", mbDate());
$date_max = mbGetValueFromGetOrSession("_date_max", mbDate());

// Id du praticien selectionné
$prat = mbGetValueFromGetOrSession("chir");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($prat);
$praticien->loadRefBanque();

// Extraction des elements qui composent le numero de compte
$compte_banque  = substr($praticien->compte, 0, 5);
$compte_guichet = substr($praticien->compte, 5, 5);
$compte_numero  = substr($praticien->compte, 10, 11);
$compte_cle     = substr($praticien->compte, 21, 2);

$consult = new CConsultation();

$whereConsult["mode_reglement"] = " = 'cheque' ";
$whereConsult["patient_regle"] = " = '1' ";
$whereConsult["date_paiement"] = " BETWEEN '$date_min' AND '$date_max' ";

// Nombre de cheques remis
$nbRemise = 0;

// Montant total des cheques
$montantTotal = 0;

$listConsult = array();
$ljoin = array();
$whereConsult["chir_id"] = "= '$praticien->_id'";
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

// Recherche des consultations 
$consults = $consult->loadList($whereConsult,null,null,null,$ljoin);

// Chargements des consultations
foreach($consults as $key=>$consult){
	$consult->loadRefPraticien();
	$consult->loadRefPatient();
	$consult->loadRefBanque();
	$listConsult[$key] = $consult;
	$nbRemise++;
	$montantTotal += $consult->_somme;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"            , $praticien      );
$smarty->assign("listConsult"          , $listConsult    );
$smarty->assign("date"                 , $date           );
$smarty->assign("compte_banque"        , $compte_banque  );
$smarty->assign("compte_guichet"       , $compte_guichet );
$smarty->assign("compte_numero"        , $compte_numero  );
$smarty->assign("compte_cle"           , $compte_cle     );
$smarty->assign("montantTotal"         , $montantTotal   );
$smarty->assign("nbRemise"             , $nbRemise       );
$smarty->display("print_bordereau.tpl");

?>