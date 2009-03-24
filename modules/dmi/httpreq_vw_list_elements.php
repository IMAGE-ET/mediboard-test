<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Alexis Granger
 */

global $can, $g;
$can->needsRead();

// Recuperation des categories
$category_class = mbGetValueFromGetOrSession("category_class");
$category = new $category_class;
$category->group_id = $g;
$categories = $category->loadMatchingList();

// Chargement de tous les dmis
foreach ($categories as &$_category) {
  $_category->loadRefsElements();
  foreach ($_category->_ref_elements as &$_element) {
  	$_element->loadExtProduct();
  	$_element->_ext_product->loadRefsFwd();
  }
}

switch($category_class){
  case 'CDMICategory':
    $object_class = 'CDMI';
    break;
  case 'CCategoryDM':
    $object_class = 'CDM';
    break; 
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->assign("category_class", $category_class);
$smarty->assign("object_class", $object_class);
$smarty->display("inc_list_elements.tpl");
?>