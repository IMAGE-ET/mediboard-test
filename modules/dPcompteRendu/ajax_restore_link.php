<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$modele_id = CValue::get("modele_id");
$do_it      = CValue::get("do_it", 0);

$modele = new CCompteRendu;
$modele->load($modele_id);

$where = array();
$where["object_class"] = " = '$modele->object_class'";
$where["nom"] = " = '".addslashes($modele->nom)."'";
$where["object_id"] = "IS NOT NULL";
$where["modele_id"] = "IS NULL";

$nb = $modele->countList($where);

if ($do_it) {
  $docs = $modele->loadList($where, null, "100");
  $converted = 0;
  if ($nb == 0) {
    CApp::rip();
  }
  foreach ($docs as $_doc) {
    $_doc->loadTargetObject();
    if (!$_doc->_ref_object->_id) {
      // Objet référencé qui n'existe plus.
      // Suppression du document.
      $_doc->delete();
      continue;
    }
    $_doc->modele_id = $modele_id;
    
    if ($msg = $_doc->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    else {
      $converted++;
    }
  }
  
  CAppUI::stepAjax("$converted/$nb documents restaurés pour le modèle $modele->nom", $converted == $nb ? UI_MSG_OK : UI_MSG_WARNING);
}
else {
  
  if ($nb) {
    CAppUI::stepAjax($nb . " compte-rendus trouvés qui peuvent être associés (modèle $modele->nom)");
  }
}
?>