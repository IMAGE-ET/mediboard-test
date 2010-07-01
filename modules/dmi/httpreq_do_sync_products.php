<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;
CCanDo::checkAdmin();

set_time_limit(360);
ini_set('memory_limit', '128M');

$category_id  = CValue::get('category_id');
$object_class = CValue::get('object_class');

$category = new CProductCategory();
if (!$category->load($category_id)) {
  CAppUI::stepAjax('Veuillez choisir une catégorie de produits correspondant au livret thérapeutique de l\'établissement', UI_MSG_ERROR);
  return;
}

$messages = array();

// Chargement de la liste des dmi de l'etablissement
$object_list = new $object_class;
$object_list = $object_list->loadGroupList();

foreach ($object_list as $object) {
  // On ne synchronise que les produits qui sont dans le livret Therapeutique
  if(!$object->in_livret){
    continue;
  }
	$object->loadExtProduct();
	if (!$object->_produit_existant) {
	  $product = new CProduct();
	  $product->code = $object->code;
	  $product->name = $object->nom;
	  $product->description = $object->description;
	  $product->category_id = $category_id;
	  $product->quantity = 1;
	  if($msg = $product->store()) {
	  	if (!isset($messages[$msg])) $messages[$msg] = 0;
      $messages[$msg]++;
	  }
	  
	  $stock = new CProductStockGroup();
	  $stock->group_id = CGroups::loadCurrent()->_id;
	  $stock->product_id = $product->_id;
	  $stock->quantity = 1;
	  $stock->order_threshold_min = 1;
	  $stock->order_threshold_max = 2;
	  
	  if($msg = $stock->store()) {
      if (!isset($messages[$msg])) $messages[$msg] = 0;
      $messages[$msg]++;
    }
	}
}

foreach ($messages as $msg => $count) {
	CAppUI::stepAjax("$msg x $count", UI_MSG_ALERT);
}
CAppUI::stepAjax('Synchronisation des produits terminée', UI_MSG_OK);

// Sauvegarde de la catégorie en variable de config
$conf = new CMbConfig();
$data = array();
$data['dmi'][$object_class]['product_category_id'] = $category_id;
if ($conf->update($data, true)) {
  CAppUI::stepAjax('Enregistrement de la catégorie de produits effectuée', UI_MSG_OK);
}

?>