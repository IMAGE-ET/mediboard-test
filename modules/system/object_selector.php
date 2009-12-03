<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$selClass  = CValue::get("selClass");
$onlyclass = CValue::get("onlyclass");
$keywords  = CValue::get("keywords");
$object_id = CValue::get("object_id");

// Liste des classes
$classes = array();
foreach (getInstalledClasses() as $class) {
  $object = @new $class;
  $classes[$class] = array_keys($object->getSeekables());
}

$list = array();
if ($selClass) {
  if (!array_key_exists($selClass, $classes)) {
    trigger_error("The class '$selClass' is not installed", E_USER_ERROR);
    return;
  }

  $object = new $selClass;
  
  // Search with keywords
  if ($keywords) {
    $list = $object->seek($keywords);
	  foreach ($list as $key => $value) {
	    $list[$key]->loadRefsFwd();
	    if(!$list[$key]->canRead()) {
	      unset($list[$key]);
	    }
	  }
  }

  // Search with id
  if ($object_id) {
    $object->load($object_id);
    $list = $object->_id ? array($object->_id => $object) : array();
  }
}

// Création du template
$smarty = new CSmartyDP();

if ($selClass){
  $smarty->assign("list"     , $list);
}

$smarty->assign("classes"  , $classes  );
$smarty->assign("keywords" , $keywords );
$smarty->assign("object_id", $object_id);
$smarty->assign("selClass" , $selClass );
$smarty->assign("onlyclass", $onlyclass);
$smarty->display("object_selector.tpl");
?>
