<?php 

/**
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$owner_guid = CView::post("owner_guid", "str");

CView::checkin();

$owner = CMbObject::loadFromGuid($owner_guid);

if (!$owner || !$owner->_id) {
  CAppUI::stepMessage(UI_MSG_WARNING, "Le propriétaire souhaité n'existe pas.");
}

$user_id     = "";
$function_id = "";
$group_id    = "";

switch ($owner->_class) {
  case "CMediusers":
  default:
    $user_id = $owner->_id;
    break;
  case "CFunctions";
    $function_id = $owner->_id;
    break;
  case "CGroups":
    $group_id = $owner->_id;
}

$file = $_FILES['datafile'];

if (strtolower(pathinfo($file['name'] , PATHINFO_EXTENSION) !== "xml")) {
  CAppUI::stepAjax("Fichier non reconnu", UI_MSG_ERROR);
  CApp::rip();
}

$doc = file_get_contents($file['tmp_name']);

$xml = new CMbXMLDocument(null);
$xml->loadXML($doc);

$root = $xml->firstChild;

if ($root->nodeName == "modeles") {
  $root = $root->childNodes;
}
else {
  $root = array($xml->firstChild);
}

$modeles_ids_xml = array();

$components = array("header_id", "footer_id", "preface_id", "ending_id");

foreach ($root as $_modele) {
  $modele = new CCompteRendu();
  $modele->user_id     = $user_id;
  $modele->function_id = $function_id;
  $modele->group_id    = $group_id;

  // Mappings des champs principaux
  foreach ($_modele->childNodes as $_node) {
    if (in_array($_node->nodeName, CCompteRendu::$fields_import_export)) {
      $modele->{$_node->nodeName} = $_node->nodeValue;
    }
  }

  $modele->nom = utf8_decode($modele->nom);

  // Mapping de l'entête, pieds de page, introduction, conclusion
  foreach ($components as $_component) {
    if ($modele->$_component) {
      $modele->$_component = $modeles_ids[$modele->$_component];
    }
  }

  // Recherche de la catégorie
  $cat = utf8_decode($_modele->getAttribute("cat"));
  if ($cat) {
    $categorie = new CFilesCategory();
    $categorie->nom = $cat;
    if (!$categorie->loadMatchingObject()) {
      $categorie->store();
    }
    $modele->file_category_id = $categorie->_id;
  }

  if ($msg = $modele->store()) {
    CAppUI::stepAjax($modele->nom . " - " . $msg, UI_MSG_ERROR);
    continue;
  }

  CAppUI::stepAjax($modele->nom . " - " . CAppUI::tr("CCompteRendu-msg-create"), UI_MSG_OK);


  // On garde la référence entre l'id provenant du xml et l'id en base
  $modeles_ids[$_modele->getAttribute("modele_id")] = $modele->_id;
}

CAppUI::js("window.opener.getForm('filterModeles').onsubmit()");