<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Alexis Granger
 */

global $can;
$can->needsRead();

// Recuperation de la classe de la categorie
$element_class = mbGetValueFromGetOrSession("element_class");
$element_id = mbGetValueFromGetOrSession("element_id");

// Chargement de l'element selectionné
$element = new $element_class;
$element->load($element_id);

$generate_code = mbGetValueFromGet("generate_code", false);
if($generate_code){
	$element->category_dm_id = mbGetValueFromGet("category_dm_id");
	$element->nom = mbGetValueFromGet("nom");
	$element->description = mbGetValueFromGet("description");
	$element->in_livret = mbGetValueFromGet("in_livret");
	
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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("element", $element);
$smarty->assign("categories", $categories);
$smarty->assign("element_class", $element_class);
$smarty->display("inc_edit_element.tpl");

?>