<?php

/**
 * Widget des documents
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::check();

$object_class = CValue::getOrSession("object_class");
$object_id    = CValue::getOrSession("object_id");
$user_id      = CValue::getOrSession("praticien_id");
$only_docs    = CValue::get("only_docs", 0);

CSessionHandler::writeClose();

// Chargement de l'objet cible
$object = new $object_class;
if (!$object instanceof CMbObject) {
  trigger_error("object_class should be an CMbObject", E_USER_WARNING);
  return;
}

$object->load($object_id);
if (!$object->_id) {
  trigger_error("object of class '$object_class' could not be loaded with id '$object_id'", E_USER_WARNING);
  return;
}

$object->canDo();

$user = CMediusers::get();

// Praticien concerné
if (!$user->isPraticien() && $user_id) {
  $user = new CMediusers();
  $user->load($user_id);
}

$user->loadRefFunction();
$user->_ref_function->loadRefGroup();
$user->canDo();

if ($object->loadRefsDocs()) {
  foreach ($object->_ref_documents as $_doc) {
    $_doc->loadRefCategory();
    $_doc->isLocked();
    $_doc->canDo();
  }
}

// Compter les modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette();
$modele_etiquette->object_class = $object_class;
$modele_etiquette->group_id = CGroups::loadCurrent()->_id;
$nb_modeles_etiquettes = $modele_etiquette->countMatchingList();

$nb_printers = 0;

if (CModule::getActive("printing")) {
  // Chargement des imprimantes pour l'impression d'étiquettes
  $user_printers = CMediusers::get();
  $function      = $user_printers->loadRefFunction();
  $nb_printers   = $function->countBackRefs("printers");
}

$compte_rendu = new CCompteRendu();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"            , $user);
$smarty->assign("object"               , $object);
$smarty->assign("mode"                 , CValue::get("mode"));
$smarty->assign("notext"               , "notext");
$smarty->assign("nb_printers"          , $nb_printers);
$smarty->assign("nb_modeles_etiquettes", $nb_modeles_etiquettes);
$smarty->assign("can_create_docs"      , $compte_rendu->canCreate($object));

$smarty->display($only_docs ? "inc_widget_list_documents.tpl" : "inc_widget_documents.tpl");
