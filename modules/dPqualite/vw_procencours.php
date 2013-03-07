<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();
$user = CUser::get();

$doc_ged_id = CValue::getOrSession("doc_ged_id",0);

$docGed = new CDocGed;
if(!$docGed->load($doc_ged_id) || $docGed->etat==0){
  // Ce document n'est pas valide
  $doc_ged_id = null;
  CValue::setSession("doc_ged_id");
  $docGed = new CDocGed;
}else{
  $docGed->loadLastActif();
  $docGed->loadRefs();
}
$docGed->loadLastEntry();
$docGed->_lastentry->loadFile();

if($docGed->etat==CDocGed::TERMINE){
  $docGed->_lastentry = new CDocGedSuivi;
  $docGed->_lastentry->date = CMbDT::dateTime();
}

//Procédure Terminé et/ou Refusé
$procTermine = CDocGed::loadProcTermineOuRefuse($user->_id);
foreach($procTermine as $keyProc => &$currProc){
  $currProc->loadRefs();
  $currProc->getEtatRedac();
  $currProc->loadLastActif();
  $currProc->loadLastEntry();
  $currProc->loadFirstEntry();
}

// Procédure en Cours de demande
$procDemande = CDocGed::loadProcDemande($user->_id);
foreach($procDemande as $keyProc => &$currProc){
  $currProc->loadRefs();
  $currProc->getEtatRedac();
  $currProc->loadLastActif();
  $currProc->loadLastEntry();
}

// Procédure en Attente de Rédaction
$procEnCours = CDocGed::loadProcRedacAndValid($user->_id);
foreach($procEnCours as $keyProc => &$currProc){
	$currProc->loadRefs();
  $currProc->getEtatRedac();
  $currProc->loadLastEntry();
}

// Liste des Etablissements selon Permissions
$mediuser = new CMediusers();
$etablissements = $mediuser->loadEtablissements(PERM_READ);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("procTermine"    , $procTermine);
$smarty->assign("procDemande"    , $procDemande);
$smarty->assign("procEnCours"    , $procEnCours);
$smarty->assign("docGed"         , $docGed);

$smarty->display("vw_procencours.tpl");

?>