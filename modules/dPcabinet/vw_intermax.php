<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->edit &= CAppUI::pref("GestionFSE");
$can->needsEdit();

if ($can->admin) {
	$intermaxFunctions = array(
	  "Fonctions Intégrées" => array(
	    "Lire CPS",
	    "Lire Vitale",
	    "Consulter Vitale",
	    "Formater FSE",
	  ),
	  "Générales" => array(
	    "Professionnels de santé",
	    "Feuilles de soins",
	    "Visites",
	    "Télétransmission",
	    "Liaison comptable",
	  ),
	  "Préférences" => array(
	    "Configuration",
	    "Coordonnées",
	    "Préférences Liaison comptable",
	    "Fichiers RSP",
	    "Tiers-payant",
	    "Type Emetteur",
	  ),
	  "Fichiers de base" => array(
	    "Titres",
	    "Jours fériés",
	    "Communes",
	    "Prescripteurs",
	    "Lettres clés",
	    "Organismes AMO",
	    "Organismes AMC",
	    "Centre de service",
	    "Thésaurus",
	  ),
	  "Utilitaires" => array(
	    "Modification référentiel CCAM",
	    "Vérification des données",
	    "Recalcul des soldes patients",
	    "Emettre vers le CNDA",
	    "Déblocage CPS",
	    "Mode de Trace",
	    "Initialisation des compteurs",
	  ),
	);
}
else {
	$intermaxFunctions = array(
	  "Fonctions Intégrées" => array(
	    "Lire CPS",
	    "Lire Vitale",
	  ),
	  "Générales" => array(
	    "Feuilles de soins",
	    "Télétransmission",
	  ),
	  "Préférences" => array(
	    "Configuration",
	    "Coordonnées",
	    "Fichiers RSP",
	    "Tiers-payant",
	  ),
	);
}

// Praticiens autorisés
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
$prats = in_array(CUser::$types[$mediuser->_user_type], array("Administrator", "Secrétaire")) ?
  $mediuser->loadPraticiens(PERM_READ) :
  array($mediuser->_id => $mediuser);
foreach($prats as $_prat) {
  $_prat->loadIdCPS();
}
  
// Chargement des FSE trouvées
$fse = @new CLmFSE();
$fse->S_FSE_MODE_SECURISATION = CValue::get("S_FSE_MODE_SECURISATION");
$fse->_date_min = mbDate();
$fse->_date_max = mbDate("+ 1 day");

$lot = @new CLmLot();
$lot->_date_min = mbDate();
$lot->_date_max = mbDate("+ 1 day");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->assign("prats", $prats);
$smarty->assign("fse", $fse);
$smarty->assign("lot", $lot);
$smarty->display("vw_intermax.tpl");

