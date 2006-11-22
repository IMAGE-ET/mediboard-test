<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m, $g;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$doc_ged_id = mbGetValueFromGetOrSession("doc_ged_id",0);
$fileSel = new CFile;

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id) || $docGed->etat!=0){
  // Ce document n'est pas valide ou n'est pas un modèle
  $doc_ged_id = null;
  mbSetValueToSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastEntry();
  if(!$docGed->_lastentry->doc_ged_suivi_id){
    // Ce document n'a pas de modèle
    $doc_ged_id = null;
    mbSetValueToSession("doc_ged_id");
    $docGed = new CDocGed;  
  }else{
    $docGed->_lastentry->loadFile();
  }
}

if(!$docGed->_lastentry){
  $docGed->loadLastEntry();
}

// Modèles de procédure
$modeles = new CDocGed;
$where = array();
$where["doc_ged.etat"]   = "= '0'";
$where["group_id"] = "= '$g'";
$order = "titre ASC";
$ljoin = array();
$ljoin["doc_ged_suivi"] = "doc_ged.doc_ged_id = doc_ged_suivi.doc_ged_id";

$modeles = $modeles->loadList($where, $order, null, null, $ljoin);
foreach($modeles as $keyProc=>$currProc){
  $modeles[$keyProc]->loadLastEntry();
}

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("modeles"        , $modeles);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("fileSel"        , $fileSel);

$smarty->display("vw_modeles.tpl");
?>
