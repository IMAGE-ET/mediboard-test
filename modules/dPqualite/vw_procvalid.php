<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualité
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsAdmin();


$doc_ged_id        = mbGetValueFromGetOrSession("doc_ged_id",0);
$procAnnuleVisible = mbGetValueFromGetOrSession("procAnnuleVisible" , 0);
$lastactif         = mbGetvalueFromGet("lastactif", 0);

$docGed = new CDocGed;
$listCategories = array();
$listThemes     = array();
$listChapitres  = array();
if(!$docGed->load($doc_ged_id) || $docGed->etat==0){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  $docGed->loadRefs();

  // Liste des Catégories
  $categorie = new CCategorieDoc;
  $listCategories = $categorie->loadlist(null,"code");
  
  // Liste des Thèmes
  $theme = new CThemeDoc;
  $where = array();
  if($docGed->group_id) {
    $where [] = "group_id = '$docGed->group_id' OR group_id IS NULL";
  } else {
    $where ["group_id"] = "IS NULL";
  }
  $listThemes = $theme->loadlist($where,"group_id, nom");
  
  // Liste des Chapitres
  $chapitre = new CChapitreDoc;
  $where = array();
  $where ["pere_id"]  = "IS NULL";
  if($docGed->group_id) {
    $where [] = "group_id = '$docGed->group_id' OR group_id IS NULL";
  } else {
    $where ["group_id"] = "IS NULL";
  }
  $listChapitres = $chapitre->loadlist($where,"group_id, code");
  foreach($listChapitres as &$_chapitre) {
    $_chapitre->loadChapsDeep(); 
  }
}

$docGed->loadLastEntry();

// Procédure en Cours de demande
$procDemande = new CDocGed;
$procDemande = $procDemande->loadProcDemande();
foreach($procDemande as $keyProc => $currProc){
  $procDemande[$keyProc]->loadRefs();
  $procDemande[$keyProc]->getEtatRedac();
  $procDemande[$keyProc]->loadLastActif();
  $procDemande[$keyProc]->loadLastEntry();
}

// Procédure non terminé Hors demande
$procEnCours = new CDocGed;
$procEnCours = $procEnCours->loadProcRedacAndValid();
foreach($procEnCours as $keyProc => $currProc){
  $procEnCours[$keyProc]->loadRefs();
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
    $procTermine[$keyProc]->loadRefs();
    $procTermine[$keyProc]->getEtatValid();
    $procTermine[$keyProc]->loadLastEntry();
  }
}

$versionDoc = array();
if($docGed->version){
  $versionDoc[] = ($docGed->version)+ 0.1;
  $versionDoc[] = intval($docGed->version)+1;
}else{
  $versionDoc[] = "1";
}
// Création du template
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
$smarty->assign("versionDoc"        , $versionDoc);

$smarty->display("vw_procvalid.tpl");
?>
