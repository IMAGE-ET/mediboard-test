<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m, $can, $g;
$can->needsAdmin();

$schemaPath = "modules/$m/xml/elementsPrescription.xsd";
$group_id = CValue::post("group_id");

// Chargement des etablissements
$group = new CGroups();
$groups = $group->loadList();

// Affichage du template
$smarty = new CSmartyDP;
$smarty->assign("schemaPath", $schemaPath);
$smarty->assign("groups", $groups);
$smarty->display("import_elements_prescription.tpl");

$doc = new CMbXMLDocument();
$doc->setSchema($schemaPath);
$docPath = @$_FILES["docPath"]["tmp_name"];


// No file, do nothing
if (!$docPath) {
  return;
}

// Valid file ?
if ($msg = $doc->loadAndValidate($docPath)) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}
  
CAppUI::stepAjax("Catalogue d'élements de prescriptions valide", UI_MSG_OK);

// Import catalogue
$domCatalogue = new SimpleXMLElement(file_get_contents($docPath));
foreach ($domCatalogue->chapitre as $domChapitre) {
  $chapitre = (string) $domChapitre["type"];
  foreach ($domChapitre->categorie as $domCategorie) {
    $categorie = new CCategoryPrescription();
    $nom = utf8_decode((string) $domCategorie->nom[0]);
    
    $where = array();
    $where["chapitre"] = " = '$chapitre'";
    $where["nom"] = "= '.".addslashes($nom)."'";

    if($group_id == "no_group"){
		  $where["group_id"] = " IS NULL";
		} else {
		  $where["group_id"] = " = '$group_id'";
		}
    $categorie->escapeValues();
    $categorie->loadObject($where);
    
    // si la categorie n'existe pas, on la crée
    if(!$categorie->_id){
      $categorie->chapitre = $chapitre;
      $categorie->nom = $nom;
      if($group_id != "no_group"){
        $categorie->group_id = $group_id;
      }
    }
    $categorie->header = utf8_decode((string) $domCategorie->header[0]);
    $categorie->color = utf8_decode((string) $domCategorie->color[0]);
    $categorie->prescription_executant = utf8_decode((string) $domCategorie->prescription_executant[0]);
    $categorie->cible_importante = utf8_decode((string) $domCategorie->cible_importante[0]);
    $categorie->only_cible = utf8_decode((string) $domCategorie->only_cible[0]);
    $categorie->unescapeValues();
        
    $categorie_id = $categorie->_id;
    $categorie->description = utf8_decode((string) $domCategorie->description[0]);

    if ($msg = $categorie->store()) {
      CAppUI::stepAjax("Erreur import categorie: $msg", UI_MSG_WARNING);
      continue;
    }
    
    $msg = !$categorie_id ? "Ajout catégorie" : 
       ($categorie->_ref_last_log ? "Mise à jour catégorie" : "Catégorie inchangée");
    CAppUI::stepAjax($msg, UI_MSG_OK);
	       
    foreach ($domCategorie->element as $domElement) {
      $element = new CElementPrescription();
      $element->category_prescription_id = $categorie->_id;
      $element->libelle = utf8_decode((string) $domElement->libelle);
      
      $element->libelle = addslashes($element->libelle);
      $element->loadMatchingObject();
      $element->libelle = stripslashes($element->libelle);
      
      $element_id = $element->_id;
      $element->description = utf8_decode((string) $domElement->description);
      $element->color = utf8_decode((string) $domElement->color);
      $element->prescriptible_kine = utf8_decode((string) $domElement->prescriptible_kine);
      $element->prescriptible_infirmiere = utf8_decode((string) $domElement->prescriptible_infirmiere);
      $element->prescriptible_AS = utf8_decode((string) $domElement->prescriptible_AS);
      $element->rdv = utf8_decode((string) $domElement->rdv);
      
	    if ($msg = $element->store()) {
	      CAppUI::stepAjax("Erreur import élement: $msg", UI_MSG_WARNING);
	      continue;
	    }
	    
	    $msg = !$element_id ? "Ajout élément" : 
	       ($element->_ref_last_log ? "Mise à jour élément" : "Elément inchangé");
	    CAppUI::stepAjax($msg, UI_MSG_OK);
    } 
  }
}


?>