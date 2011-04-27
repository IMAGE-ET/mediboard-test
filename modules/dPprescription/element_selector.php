<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can, $m, $g;

$type = CValue::get("type");
$libelle = CValue::get("libelle");
$user_id = CValue::get("user_id");

if(!$libelle){
  $libelle = '%';
}

$categories = array();
$tabElements = array();
$elements = array();

// Chargement de la liste des categories
$category_prescription = new CCategoryPrescription();
$where["chapitre"] = "= '$type'";
$where[] = "group_id = '$g' OR group_id IS NULL";

if($libelle){
  $categories = $category_prescription->loadList($where);
}
// Chargement des elements des categories precedements chargées
if($libelle){
	$element_prescription = new CElementPrescription();
	$where = array();
	$where["category_prescription_id"] = CSQLDataSource::prepareIn(array_keys($categories));
	$where["libelle"] = "LIKE '$libelle%'";
	$where["cancelled"] = " = '0'";
	$order = "libelle";
	
	if($user_id){
	  $user = new CMediusers();
    $user->load($user_id);
	
	  if($user->isInfirmiere()){
	    $where["prescriptible_infirmiere"] = " = '1'";
	  } elseif ($user->isAideSoignant()){
	    $where["prescriptible_AS"] = " = '1'";
	  } elseif ($user->isKine()){
	    $where["prescriptible_kine"] = " = '1'";
	  }
  }
	
	$elements = $element_prescription->loadList($where, $order);
}
// Rangement des elements trouvés par categorie
foreach($elements as $element){
	$tabElements[$element->category_prescription_id][] = $element;
}

if($libelle == '%'){
  $libelle = '';
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type", $type);
$smarty->assign("libelle", $libelle);
$smarty->assign("elements", $elements);
$smarty->assign("categories", $categories);
$smarty->assign("tabElements", $tabElements);

$smarty->display("element_selector.tpl");

?>