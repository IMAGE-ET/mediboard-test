<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision$
* @author Stéphanie Subilia
*/

global $can, $AppUI;

$keywords = mbGetValueFromPost("_view");

if($can->read && $keywords) {
	 $product = new CProduct();
	 $where[] = "name LIKE '$keywords%' OR code LIKE '$keywords%'";
	 $matches = $product->loadList($where,'name',10);
	 
	 // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("keywords", $keywords);
  $smarty->assign("matches", $matches);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_product_autocomplete.tpl");
}
?>
