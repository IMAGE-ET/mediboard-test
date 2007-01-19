<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualité
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$doc_ged_id = mbGetValueFromGetOrSession("doc_ged_id",0);
$selTheme   = mbGetValueFromGetOrSession("selTheme", 0);
$fileSel = null;

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id)){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  if(!$docGed->_lastactif->doc_ged_suivi_id || $docGed->annule){
    // Ce document n'est pas Terminé ou est sspendu
    $doc_ged_id = null;
    mbSetValueToSession("doc_ged_id");
    $docGed = new CDocGed;	
  }else{
    $docGed->_lastactif->loadFile();
    $docGed->loadRefsBack();
  }
}

// Liste des Thèmes
$listThemes = new CThemeDoc;
$listThemes = $listThemes->loadlist(null,"nom");

// Procédure active et non annule
$procedures = new CDocGed;

$where = array();
$where["annule"]   = "= '0'";
$where["group_id"] = "= '$g'";
$where["actif"]    = "= '1'";
if($selTheme!=0){
  $where["doc_theme_id"] = "= '$selTheme'";
}
$ljoin = array();
$ljoin["doc_ged_suivi"] = "doc_ged.doc_ged_id = doc_ged_suivi.doc_ged_id";
    
$procedures = $procedures->loadList($where, null, null, null, $ljoin);
foreach($procedures as $keyProc=>$currProc){
  $procedures[$keyProc]->loadRefsBack();
  $procedures[$keyProc]->loadLastActif();
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP();

$smarty->assign("selTheme"       , $selTheme);
$smarty->assign("listThemes"     , $listThemes);
$smarty->assign("procedures"     , $procedures);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("fileSel"        , $fileSel);

$smarty->display("vw_procedures.tpl");
?>
