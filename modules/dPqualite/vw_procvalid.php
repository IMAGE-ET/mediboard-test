<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualité
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsAdmin();


$doc_ged_id        = mbGetValueFromGetOrSession("doc_ged_id",0);
$procAnnuleVisible = mbGetValueFromGetOrSession("procAnnuleVisible" , 0);
$lastactif         = mbGetvalueFromGet("lastactif", 0);

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id) || $docGed->etat==0){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  $docGed->loadRefsBack();
}

$docGed->loadLastEntry();

// Procédure en Cours de demande
$procDemande = new CDocGed;
$procDemande = $procDemande->loadProcDemande();
foreach($procDemande as $keyProc => $currProc){
  $procDemande[$keyProc]->loadRefsBack();
  $procDemande[$keyProc]->getEtatRedac();
  $procDemande[$keyProc]->loadLastActif();
  $procDemande[$keyProc]->loadLastEntry();
}

// Procédure non terminé Hors demande
$procEnCours = new CDocGed;
$procEnCours = $procEnCours->loadProcRedacAndValid();
foreach($procEnCours as $keyProc => $currProc){
  $procEnCours[$keyProc]->loadRefsBack();
  $procEnCours[$keyProc]->getEtatValid();
  $procEnCours[$keyProc]->loadLastEntry();
}

// Procédures Terminée et Annulée
$procTermine = new CDocGed;
$where = array();
$where["annule"] = "= '1'";
$procTermine = $procTermine->loadList($where);
if($procAnnuleVisible){
  foreach($procTermine as $keyProc => $currProc){
    $procTermine[$keyProc]->loadRefsBack();
    $procTermine[$keyProc]->getEtatValid();
    $procTermine[$keyProc]->loadLastEntry();
  }
}

$order = "code";
// Liste des Catégories
$listCategories = new CCategorieDoc;
$listCategories = $listCategories->loadlist(null,$order);

// Liste des Thèmes
$listThemes = new CThemeDoc;
$listThemes = $listThemes->loadlist(null,"nom");

// Liste des Chapitres
$listChapitres = new CChapitreDoc;
$where = array("pere_id" => "IS NULL");
$listChapitres = $listChapitres->loadlist($where,$order);
foreach($listChapitres as &$_chapitre) {
  $_chapitre->loadChapsDeep(); 
}
//mbTrace($listChapitres);

$versionDoc = array();
if($docGed->version){
  $versionDoc[] = ($docGed->version)+ 0.1;
  $versionDoc[] = intval($docGed->version)+1;
}else{
  $versionDoc[] = "1";
}
// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP();

$smarty->assign("lastactif"         , $lastactif);
$smarty->assign("procAnnuleVisible" , $procAnnuleVisible);
$smarty->assign("procTermine"       , $procTermine);
$smarty->assign("procDemande"       , $procDemande);
$smarty->assign("procEnCours"       , $procEnCours);
$smarty->assign("listCategories"    , $listCategories);
$smarty->assign("listThemes"        , $listThemes);
$smarty->assign("listChapitres"     , $listChapitres);
$smarty->assign("docGed"            , $docGed);
$smarty->assign("g"                 , $g);
$smarty->assign("versionDoc"        , $versionDoc);
$smarty->assign("user_id"           , $AppUI->user_id);

$smarty->display("vw_procvalid.tpl");
?>
