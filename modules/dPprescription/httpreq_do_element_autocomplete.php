<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$category = mbGetValueFromGet("category");
$libelle = mbGetValueFromPost($category, "aaa");

// Chargement de la liste des categories
$category_prescription = new CCategoryPrescription();
$category_prescription->chapitre = $category;
$categories = $category_prescription->loadMatchingList();

// Chargement des elements des categories precedements chargées
$ds = CSQLDataSource::get("std");
$element_prescription = new CElementPrescription();
$where = array();
$where["category_prescription_id"] = $ds->prepareIn(array_keys($categories));
$where["libelle"] = "LIKE '%$libelle%'";
$elements = $element_prescription->loadList($where);
foreach($elements as &$element){
	$element->loadRefCategory();
}
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("libelle", $libelle);
$smarty->assign("elements", $elements);
$smarty->assign("nodebug", true);
$smarty->display("httpreq_do_element_autocomplete.tpl");

?>