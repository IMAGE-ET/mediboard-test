<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$selClass  = mbGetValueFromGet("selClass");
$onlyclass = mbGetValueFromGet("onlyclass");
$keywords  = mbGetValueFromGet("keywords");
$object_id = mbGetValueFromGet("object_id");

// Liste des classes
$classes = array();
foreach (getInstalledClasses() as $class) {
  $object = @new $class;
  $classes[$class] = array_keys($object->getSeekables());
}

$list = array();
if ($selClass) {
  if (!array_key_exists($selClass, $classes)) {
    trigger_error("Uninstalled class '$selClass'", E_USER_ERROR);
    return;
  }

  $object = new $selClass;
  
  // Search with keywords
  if ($keywords) {
		$keywords_search = explode(" ", trim($keywords));
		$keywords_search = array_filter($keywords_search);
    $list = $object->seek($keywords_search);
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
