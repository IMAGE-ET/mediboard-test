<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$prat_id         = CValue::getOrSession("selPrat");
$compte_rendu_id = CValue::getOrSession("compte_rendu_id");

$group = CGroups::loadCurrent();
$listEtab = array($group);

$where = array(
  "group_id" => "= '$group->_id'"
);
$listFunc = new CFunctions();
$listFunc = $listFunc->loadListWithPerms(PERM_EDIT, $where, "text");

// Liste des praticiens accessibles
$listUser = new CMediusers();
//$listUser = $listUser->loadPraticiens(PERM_EDIT);
$listUser = $listUser->loadUsers(PERM_EDIT);

$mediuser = CMediusers::get();

// L'utilisateur est-il praticien?
if (!$prat_id) {
  if ($mediuser->isPraticien()) {
    $prat_id = $mediuser->user_id;
    CValue::setSession("selPrat", $prat_id);
  }
}

// Compte-rendu selectionné
$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);
$compte_rendu->loadContent();
$compte_rendu->loadRefsNotes();

if (!$compte_rendu->_id) {
  $compte_rendu->valueDefaults();
  $compte_rendu->author_id = CAppUI::$user->_id;
}

if ($compte_rendu->object_id) {
  $compte_rendu = new CCompteRendu();
}
else{
  $compte_rendu->loadRefCategory();
}

// Gestion du modèle
$_GET["isBody"] = $compte_rendu->type == "body";
$templateManager = new CTemplateManager($_GET);
$templateManager->editor = "ckeditor";

// L'utilisateur est il une secretaire ou un administrateur?
$secretaire = $mediuser->isFromType(array("Secrétaire", "Administrator"));

// si l'utilisateur courant est la secretaire ou le proprietaire du modele alors droit dessus, sinon, seulement droit en lecture
$droit = (!($compte_rendu->_id) ||
           ($secretaire) ||
           ($compte_rendu->user_id == $mediuser->user_id) ||
           ($compte_rendu->function_id == $mediuser->function_id) ||
           $compte_rendu->canEdit());

$templateManager->printMode = !$droit;

if ($compte_rendu->_id) {
  if ($droit) {
    $prat_id = $compte_rendu->user_id;
    $templateManager->valueMode = false;
    $templateManager->loadLists($compte_rendu->user_id, $compte_rendu->_id);
    $templateManager->loadHelpers($compte_rendu->user_id, $compte_rendu->object_class);
    $templateManager->applyTemplate($compte_rendu);
  }

  $templateManager->initHTMLArea();
}

// Class and fields
$listObjectClass     = array();
$listObjectAffichage = array();

foreach (CCompteRendu::getTemplatedClasses() as $valueClass => $localizedClassName){
  $listObjectClass[$valueClass]     = array();
  $listObjectAffichage[$valueClass] = utf8_encode($localizedClassName);
}

foreach ($listObjectClass as $keyClass => $value) {
  $listCategory = CFilesCategory::listCatClass($keyClass);
  foreach($listCategory as $keyCat=>$valueCat){
    $listObjectClass[$keyClass][$keyCat] = utf8_encode($listCategory[$keyCat]->nom);
  }
}

// Headers and footers
$component = new CCompteRendu();
$headers  = array();
$prefaces = array();
$endings  = array();
$footers  = array();

if ($compte_rendu->_id) {
  // Si modèle de fonction, on charge en fonction d'un des praticiens de la fonction
  if ($compte_rendu->user_id) {
    $owner = 'prat';
    $id = $compte_rendu->user_id;
  }
  else if ($compte_rendu->function_id) {
    $owner = 'func';
    $id = $compte_rendu->function_id;
  }
  else if ($compte_rendu->group_id) {
    $owner = 'etab';
    $id = $compte_rendu->group_id;
  } else {
    $owner = 'etab';
    $id = CGroups::loadCurrent()->_id;
  }

  $headers  = CCompteRendu::loadAllModelesFor($id, $owner, $compte_rendu->object_class, "header");
  $prefaces = CCompteRendu::loadAllModelesFor($id, $owner, $compte_rendu->object_class, "preface");
  $endings  = CCompteRendu::loadAllModelesFor($id, $owner, $compte_rendu->object_class, "ending"); 
  $footers  = CCompteRendu::loadAllModelesFor($id, $owner, $compte_rendu->object_class, "footer");
  
  if ($compte_rendu->_owner != "prat") {
    unset($headers["prat"]);
    unset($prefaces["prat"]);
    unset($endings["prat"]);
    unset($footers["prat"]);
  }
  
  if ($compte_rendu->_owner == "etab") {
    unset($headers["func"]);
    unset($prefaces["func"]);
    unset($endings["func"]);
    unset($footers["func"]);
  }
  
  switch ($compte_rendu->type) {
    case "header":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("modeles_headed");
      break;
    case "preface":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("modeles_prefaced");
      break;
    case "preface":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("modeles_ended");
      break;
    case "footer":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("modeles_footed");
  }
}

$formats = CCompteRendu::$_page_formats;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"            , $mediuser);
$smarty->assign("isPraticien"         , $mediuser->isPraticien());
$smarty->assign("user_id"             , $mediuser->_id);
$smarty->assign("prat_id"             , $prat_id);
$smarty->assign("compte_rendu_id"     , $compte_rendu_id);
$smarty->assign("listPrat"            , $listUser);
$smarty->assign("listEtab"            , $listEtab);
$smarty->assign("listFunc"            , $listFunc);
$smarty->assign("listObjectClass"     , $listObjectClass);
$smarty->assign("compte_rendu"        , $compte_rendu);
$smarty->assign("listObjectAffichage" , $listObjectAffichage);
$smarty->assign("droit"               , $droit);
$smarty->assign("headers"             , $headers);
$smarty->assign("prefaces"            , $prefaces);
$smarty->assign("endings"             , $endings);
$smarty->assign("footers"             , $footers);
$smarty->assign("formats"             , $formats);
$smarty->display("addedit_modeles.tpl");

?>