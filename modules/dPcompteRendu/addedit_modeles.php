<?php

/**
 * Création / Modification d'un modèle
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$prat_id         = CValue::getOrSession("selPrat");
$compte_rendu_id = CValue::getOrSession("compte_rendu_id");

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

// Accès aux modèles de la fonction et de l'établissement
$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_group");

if ($compte_rendu->_id) {
  if ($compte_rendu->function_id && !$access_function) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  if ($compte_rendu->group_id && !$access_group) {
    CAppUI::redirect("m=system&a=access_denied");
  }
}

$compte_rendu->loadContent();
$compte_rendu->loadRefsNotes();

if (!$compte_rendu->_id) {
  $compte_rendu->valueDefaults();
}

if ($compte_rendu->object_id) {
  $compte_rendu = new CCompteRendu();
}
else {
  $compte_rendu->loadRefCategory();
}

$compte_rendu->loadRefUser();
$compte_rendu->loadRefFunction();
$compte_rendu->loadRefGroup();

// Gestion du modèle
$_GET["isBody"] = in_array($compte_rendu->type, array("body", "preface"));
$_GET["isModele"] = 1;
$templateManager = new CTemplateManager($_GET);
$templateManager->editor = "ckeditor";

// L'utilisateur est il une secretaire ou un administrateur?
$secretaire = $mediuser->isFromType(array("Secrétaire", "Administrator"));

// si l'utilisateur courant est la secretaire ou le propriétaire du modèle alors droit dessus, sinon, seulement droit en lecture
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
    $templateManager->applyTemplate($compte_rendu);
  }

  $templateManager->initHTMLArea();
}

// Class and fields
$listObjectClass     = array();
$listObjectAffichage = array();

foreach (CCompteRendu::getTemplatedClasses() as $valueClass => $localizedClassName) {
  $listObjectClass[$valueClass]     = array();
  $listObjectAffichage[$valueClass] = utf8_encode($localizedClassName);
}

foreach ($listObjectClass as $keyClass => $value) {
  $listCategory = CFilesCategory::listCatClass($keyClass);
  foreach ($listCategory as $keyCat=>$valueCat) {
    $listObjectClass[$keyClass][$keyCat] = utf8_encode($listCategory[$keyCat]->nom);
  }
}

// Headers and footers
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
  }
  else {
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
    case "body":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("pack_links");
      break;
    case "ending":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("modeles_ended");
      break;
    case "footer":
      $compte_rendu->_count_utilisation = $compte_rendu->countBackRefs("modeles_footed");
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"           , $mediuser);
$smarty->assign("user_id"            , $mediuser->_id);
$smarty->assign("prat_id"            , $prat_id);
$smarty->assign("access_function"    , $access_function);
$smarty->assign("access_group"       , $access_group);
$smarty->assign("listObjectClass"    , $listObjectClass);
$smarty->assign("compte_rendu"       , $compte_rendu);
$smarty->assign("listObjectAffichage", $listObjectAffichage);
$smarty->assign("droit"              , $droit);
$smarty->assign("headers"            , $headers);
$smarty->assign("prefaces"           , $prefaces);
$smarty->assign("endings"            , $endings);
$smarty->assign("footers"            , $footers);

$smarty->display("addedit_modeles.tpl");
