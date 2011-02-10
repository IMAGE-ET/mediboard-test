<?php /* $Id: import_elements_prescription.php 7211 2009-11-03 12:27:08Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 7211 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m, $can, $g;
$can->needsAdmin();

$group_id = CValue::post("group_id");

// Chargement des etablissements
$group = new CGroups();
$groups = $group->loadList();

// Affichage du template
$smarty = new CSmartyDP;
$smarty->assign("schemaPath", "csv");
$smarty->assign("groups", $groups);
$smarty->display("import_elements_prescription.tpl");

$docPath = @$_FILES["docPath"]["tmp_name"];

// No file, do nothing
if (!$docPath) {
  return;
}

$fp = fopen($docPath, "r");
if (!$fp) {
  CAppUI::setMsg("Catalogue d'élements de prescriptions introuvable", UI_MSG_ERROR);
}

$n = 1;
while ($line = fgetcsv($fp, null, ",")) {
	if (!isset($line[2])) {
		CAppUI::setMsg("Ligne invalide (ligne $n)", UI_MSG_WARNING);
		continue;
	}
	
	$nom_chapitre  = trim(strtolower($line[0]));
  $nom_categorie = trim($line[1]);
  $nom_element   = trim($line[2]);
	
	if (!in_array($nom_chapitre, CCategoryPrescription::$chapitres_elt)) {
    CAppUI::setMsg("Chapitre <strong>$nom_chapitre</strong> invalide", UI_MSG_WARNING);
    continue;
	}
	
  $categorie = new CCategoryPrescription();
  
  $where = array();
  $where["chapitre"] = " = '$nom_chapitre'";
  $where["nom"] = "= '$nom_categorie'";
  
  if($group_id == "no_group"){
    $where["group_id"] = " IS NULL";
  } else {
    $where["group_id"] = " = '$group_id'";
  }
	
  $categorie->escapeValues();
  $categorie->loadObject($where);
  
  // si la categorie n'existe pas, on la crée
  if(!$categorie->_id){
    $categorie->chapitre = $nom_chapitre;
    $categorie->nom = $nom_categorie;
    if($group_id != "no_group"){
      $categorie->group_id = $group_id;
    }
  }
  
  $categorie->unescapeValues();
      
  $categorie_id = $categorie->_id;
  //$categorie->description = utf8_decode((string) $domCategorie->description[0]);

  if ($msg = $categorie->store()) {
    CAppUI::setMsg("Erreur import categorie: $msg", UI_MSG_WARNING);
    continue;
  }
  
  $msg = !$categorie_id ? "Ajout catégorie" : 
     ($categorie->_ref_last_log ? "Mise à jour catégorie" : "Catégorie inchangée");
  CAppUI::setMsg($msg, UI_MSG_OK);
  
	// Import de l'element
  $element = new CElementPrescription();
  $element->category_prescription_id = $categorie->_id;
  $element->libelle = $nom_element;
  
  $element->libelle = addslashes($element->libelle);
  $element->loadMatchingObject();
  $element->libelle = stripslashes($element->libelle);
  
  $element_id = $element->_id;
  //$element->description = utf8_decode((string) $domElement->description);
  
  if ($msg = $element->store()) {
    CAppUI::setMsg("Erreur import élement: $msg", UI_MSG_WARNING);
    continue;
  }
  
  $msg = !$element_id ? "Ajout élément" : 
     ($element->_ref_last_log ? "Mise à jour élément" : "Elément inchangé");
  CAppUI::setMsg($msg, UI_MSG_OK);
	
	$n++;
}

echo CAppUI::getMsg();
