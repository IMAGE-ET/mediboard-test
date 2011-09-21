<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$idSante400_id = CValue::get("idSante400_id");

$idSante400 = new CIdSante400;
$idSante400->load($idSante400_id);

$filter = new CIdSante400;
$filter->object_class = $idSante400->object_class;
$filter->object_id    = $idSante400->object_id;

$filter->tag = CSejour::getTagNDA(CGroups::loadCurrent()->_id);
$listIdSante400 = $filter->loadMatchingList(CGroups::loadCurrent()->_id);

$filter->tag = CSejour::getTagNDA(CGroups::loadCurrent()->_id, "tag_dossier_cancel");
$listIdSante400 = array_merge($listIdSante400, $filter->loadMatchingList());

$filter->tag = CSejour::getTagNDA(CGroups::loadCurrent()->_id, "tag_dossier_trash");
$listIdSante400 = array_merge($listIdSante400, $filter->loadMatchingList());

$filter->tag = CSejour::getTagNDA(CGroups::loadCurrent()->_id, "tag_dossier_pa");
$listIdSante400 = array_merge($listIdSante400, $filter->loadMatchingList());

$tag = CSejour::getTagNDA(CGroups::loadCurrent()->_id);

// Chargement de l'objet afin de récupérer l'id400 associé
$object = new $filter->object_class;
$object->load($filter->object_id);
$object->loadNDA(CGroups::loadCurrent()->_id);

foreach ($listIdSante400 as $_idSante400) {
  // L'identifiant 400 coché
  if ($_idSante400->_id == $idSante400_id) {
    $_idSante400->tag = CSejour::getTagNDA(CGroups::loadCurrent()->_id);
    if ($msg = $_idSante400->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR); 
    }
    continue;
  }
  // L'ancien est à mettre en trash
  if ($_idSante400->id400 == $object->_NDA) {
    $_idSante400->tag = CAppUI::conf("dPplanningOp CSejour tag_dossier_trash") .$tag;
    if ($msg = $_idSante400->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR); 
    }
  }
}

?>