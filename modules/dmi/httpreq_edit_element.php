<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Recuperation de la classe de la categorie
$element_class = CValue::getOrSession("element_class");
$element_id = CValue::getOrSession("element_id");

// Chargement de l'element selectionné
$element = new $element_class;
$element->load($element_id);
$element->loadRefProduct();
$element->_ref_product->loadRefsBack();
foreach ($element->_ref_product->_ref_references as $_ref) {
  $_ref->loadRefSociete();
}
$element->_ref_product->loadRefsLots();

$generate_code = CValue::get("generate_code", false);
if($generate_code){
	$element->category_dm_id = CValue::get("category_dm_id");
	$element->nom = CValue::get("nom");
	$element->description = CValue::get("description");
	$element->in_livret = CValue::get("in_livret");
	
	// Recherche des DM dont le code commence par DM
  $dm = new CDM();
  $where["code"] = "LIKE 'DM%'";
  $order = "dm_id DESC";
  $dms = $dm->loadList($where, $order);
  $last_dm = reset($dms);
  
  if(!$last_dm){
    $code = "DM00001";
  } else {
    $code = str_replace("DM","", $last_dm->code);
    $code++;
    $code = "DM".str_pad($code, 5, "0", STR_PAD_LEFT);
  }
  $element->code = $code;
}

// Chargement des categories
if($element_class == "CDMI"){
  $category = new CDMICategory();
  $category->group_id = CGroups::loadCurrent()->_id;
  $categories = $category->loadMatchingList();
}

if($element_class == "CDM"){
  $category = new CCategoryDM();
  $category->group_id = CGroups::loadCurrent()->_id;
  $categories = $category->loadMatchingList();
}

$lot = new CProductOrderItemReception;
$lot->quantity = 1;

$societe = new CSociete;
$list_societes = $societe->loadList(null, "name");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("element", $element);
$smarty->assign("list_societes", $list_societes);
$smarty->assign("lot", $lot);
$smarty->assign("categories", $categories);
$smarty->assign("element_class", $element_class);
$smarty->display("inc_edit_element.tpl");

?>