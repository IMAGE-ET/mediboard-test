<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualité
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}


$doc_ged_id = mbGetValueFromGetOrSession("doc_ged_id",0);

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id) || $docGed->annule){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  $docGed->loadRefsBack();
}
$docGed->loadLastEntry();
$docGed->_lastentry->loadFile();

if($docGed->etat==CDOC_TERMINE){
  $docGed->_lastentry = new CDocGedSuivi;
}

//Procédure Terminé et/ou Refusé
$procTermine = new CDocGed;
$procTermine = $procTermine->loadProcTermineOuRefuse($AppUI->user_id,0);
foreach($procTermine as $keyProc => $currProc){
  $procTermine[$keyProc]->loadRefsBack();
  $procTermine[$keyProc]->getEtatRedac();
  $procTermine[$keyProc]->loadLastActif();
  $procTermine[$keyProc]->loadLastEntry();
  $procTermine[$keyProc]->loadFirstEntry();
}


// Procédure en Cours de demande
$procDemande = new CDocGed;
$procDemande = $procDemande->loadProcDemande($AppUI->user_id,0);
foreach($procDemande as $keyProc => $currProc){
  $procDemande[$keyProc]->loadRefsBack();
  $procDemande[$keyProc]->getEtatRedac();
  $procDemande[$keyProc]->loadLastActif();
  $procDemande[$keyProc]->loadLastEntry();
}

// Procédure en Attente de Rédaction
$procEnCours = new CDocGed;
$procEnCours = $procEnCours->loadProcRedacAndValid($AppUI->user_id,0);
foreach($procEnCours as $keyProc => $currProc){
	$procEnCours[$keyProc]->loadRefsBack();
  $procEnCours[$keyProc]->getEtatRedac();
  $procEnCours[$keyProc]->loadLastEntry();
}

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("procTermine"    , $procTermine);
$smarty->assign("etablissements" , $etablissements);
$smarty->assign("procDemande"    , $procDemande);
$smarty->assign("procEnCours"    , $procEnCours);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("g"              , $g);
$smarty->assign("user_id"        , $AppUI->user_id);
$smarty->display("vw_procencours.tpl");
?>
