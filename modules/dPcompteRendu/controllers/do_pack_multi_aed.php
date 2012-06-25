<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Génération d'un document pour chaque modèle du pack
$pack_id   = CValue::post("pack_id");
$object_id = CValue::post("object_id");
$callback  = CValue::post('callback');
 
$user_id = CMediusers::get()->_id;

$pack = new CPack;
$pack->load($pack_id);

$object = new $pack->object_class;
$object->load($object_id);

$modele_to_pack = new CModeleToPack;
$modeles_to_pack = $modele_to_pack->loadAllModelesFor($pack_id);

// Sauvegarde du premier compte-rendu pour
// l'afficher dans la popup d'édition de compte-rendu

$first = reset($modeles_to_pack);

$cr_to_push = null;

foreach ($modeles_to_pack as $_modele_to_pack) {
  $modele = $_modele_to_pack->loadRefModele();
  $modele->loadContent();
  
  $template = new CTemplateManager;
  $template->isModele = false;
  
  $object->fillTemplate($template);
  
  $cr = new CCompteRendu;
  $cr->object_class = $modele->object_class;
  $cr->object_id    = $object_id;
  $cr->author_id    = $user_id;
  $cr->nom          = $modele->nom;
  $cr->file_category_id = $modele->file_category_id;
  $cr->loadContent(false);
  
  $cr->_source = $modele->_source;
  $template->applyTemplate($cr);
  
  
  if ($msg = $cr->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  
  if ($_modele_to_pack === $first) {
    $cr_to_push = $cr;
  }
}

if ($callback && $cr_to_push) {
  $fields = $cr_to_push->getProperties();
  echo CAppUI::callbackAjax($callback, $cr_to_push->_id, $fields);
}

CAppUI::setMsg(CAppUI::tr("CPack-msg-create"), UI_MSG_OK);

echo CAppUI::getMsg();

CApp::rip();

?>