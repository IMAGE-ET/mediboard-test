<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision$
* @author Stéphanie Subilia
*/

global $can, $AppUI;

$keywords = mbGetValueFromPost("_view");
$category_id = mbGetValueFromPost("category_id");

if($keywords) {
	$product = new CProduct();
	$where[] = "name LIKE '$keywords%' OR code LIKE '$keywords%'";
	if($category_id){
	  $where["category_id"] = " = '$category_id'";
	}
	$matches = $product->loadList($where,'name',10);
	 
	// Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("keywords", $keywords);
  $smarty->assign("matches", $matches);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_product_autocomplete.tpl");
}
?>
