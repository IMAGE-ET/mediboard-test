<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m, $can;
$can->needsAdmin();

$doc = new CMbXMLDocument();
$group_id = CValue::get("group_id");

$domElementsPrescriptions = $doc->addElement($doc, "elementsPrescriptions");

// Chargement des élements par chapitre et categories
$categories_par_chapitre = CCategoryPrescription::loadCategoriesByChap(null, $group_id);

foreach ($categories_par_chapitre as $chapitre => $categories) {
  $domChapitre = $doc->addElement($domElementsPrescriptions, "chapitre");
  $doc->addAttribute($domChapitre, "type", $chapitre);  
  
  $count_elements_prescription = 0;
  foreach ($categories as &$categorie) {
    $categorie->loadElementsPrescription();
    $domCategorie = $doc->addElement($domChapitre, "categorie");
    $doc->addElement($domCategorie, "nom"        , $categorie->nom);
    $doc->addElement($domCategorie, "description", $categorie->description);
    
    foreach ($categorie->_ref_elements_prescription as $element) {
    	if(!$element->cancelled){
    		$count_elements_prescription++;
	      $domElement = $doc->addElement($domCategorie, "element");
	      $doc->addElement($domElement, "libelle"    , $element->libelle);
	      $doc->addElement($domElement, "description", $element->description);
			}
    }
  }

  CAppUI::stepAjax("Chapitre '%s' : %d catégories, %d éléments", UI_MSG_OK, 
    $chapitre,
    count($categories), 
    $count_elements_prescription
  );
}

// Sauvegarde du fichier temporaire
$path = "tmp/$m/elementsPrescription.xml";
CMbPath::forceDir(dirname($path));
$doc->save($path);
$doc->load($path);

// Validation du document
$doc->setSchema("modules/$m/xml/elementsPrescription.xsd");
if (!$doc->schemaValidate()) {
  CAppUI::stepAjax("Catalogue d'élements de prescriptions invalide", UI_MSG_ERROR );
}

CAppUI::stepAjax("Catalogue d'élements de prescriptions valide", UI_MSG_OK);

// Affichage du template
$smarty = new CSmartyDP;
$smarty->assign("path", $path);
$smarty->display("inc_export_elements_prescription.tpl");
?>