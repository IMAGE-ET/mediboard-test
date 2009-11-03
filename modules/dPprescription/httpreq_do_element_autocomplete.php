<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;

$category = CValue::get("category");
$libelle = CValue::post($category, "aaa");

// Chargement de la liste des categories
$category_prescription = new CCategoryPrescription();
$where["chapitre"] = "= '$category'";
$where[] = "group_id = '$g' OR group_id IS NULL";
$categories = $category_prescription->loadList($where);

// Chargement des elements des categories precedements chargées
$element_prescription = new CElementPrescription();
$where = array();
$where["category_prescription_id"] = CSQLDataSource::prepareIn(array_keys($categories));
$where["libelle"] = "LIKE '%$libelle%'";
$where["cancelled"] = " = '0'";
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