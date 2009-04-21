<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPqualit�
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can, $g;

$can->needsRead();

$doc_ged_id   = mbGetValueFromGetOrSession("doc_ged_id");
$theme_id     = mbGetValueFromGetOrSession("theme_id");
$chapitre_id  = mbGetValueFromGetOrSession("chapitre_id");
$sort_by      = mbGetValueFromGetOrSession("sort_by", "date");
$sort_way     = mbGetValueFromGetOrSession("sort_way", "DESC");

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id)){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  if(!$docGed->_lastactif->doc_ged_suivi_id || $docGed->annule){
    // Ce document n'est pas Termin� ou est suspendu
    $doc_ged_id = null;
    mbSetValueToSession("doc_ged_id");
    $docGed = new CDocGed;	
  }else{
    $docGed->_lastactif->loadFile();
    $docGed->loadRefs();
  }
}

// Liste des Th�mes
$listThemes = new CThemeDoc;
$where = array();
$where[] = "group_id = '$g' OR group_id IS NULL";
$listThemes = $listThemes->loadlist($where,"nom");

// Liste des chapitres
$listChapitres = new CChapitreDoc;
$order = "group_id, nom";
$where = array();
$where["pere_id"] = "IS NULL";
$where[] = "group_id = '$g' OR group_id IS NULL";
$listChapitres = $listChapitres->loadlist($where,$order);
foreach($listChapitres as &$_chapitre) {
  $_chapitre->loadChapsDeep(); 
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("theme_id"       , $theme_id);
$smarty->assign("chapitre_id"    , $chapitre_id);
$smarty->assign("listThemes"     , $listThemes);
$smarty->assign("listChapitres"  , $listChapitres);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("fileSel"        , new CFile);
$smarty->assign("sort_by"        , $sort_by);
$smarty->assign("sort_way"       , $sort_way);

$smarty->display("vw_procedures.tpl");

?>