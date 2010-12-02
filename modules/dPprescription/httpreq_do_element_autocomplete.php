<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$chapitre = CValue::get("category");  // Chapitre
$category_id = CValue::get("category_id");
$libelle = CValue::post("libelle", "aaa");

if(!$category_id){
	// Chargement de la liste des categories
	$category_prescription = new CCategoryPrescription();
	if($chapitre){
	  $where["chapitre"] = "= '$chapitre'";
	}
	$group_id = CGroups::loadCurrent()->_id;
	$where[] = "group_id = '$group_id' OR group_id IS NULL";
	$categories = $category_prescription->loadList($where);
}

// Chargement des elements des categories precedements chargées
$element_prescription = new CElementPrescription();
$where = array();
if($category_id){
  $where["category_prescription_id"] = " = '$category_id'";
} else {
  $where["category_prescription_id"] = CSQLDataSource::prepareIn(array_keys($categories));
}

$where["cancelled"] = " = '0'";
$elements = $element_prescription->seek($libelle, $where);

foreach($elements as &$element){
	$element->loadRefCategory();
}
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("category_id", $category_id);
$smarty->assign("libelle", $libelle);
$smarty->assign("elements", $elements);
$smarty->assign("nodebug", true);
$smarty->display("httpreq_do_element_autocomplete.tpl");

?>