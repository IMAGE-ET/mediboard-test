<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$selClass  = mbGetValueFromGet("selClass");
$keywords  = mbGetValueFromGet("keywords");
$onlyclass = mbGetValueFromGet("onlyclass");

// Liste des classes
$listClass = getInstalledClasses();

$keywords_search = explode(" ", trim($keywords));
$keywords_search = array_filter($keywords_search);

if ($selClass) {
  if (!is_subclass_of($selClass, "CMbObject")) {
    trigger_error("Class '$selClass' is not a subclass of CMbObject", E_USER_ERROR);
    return;
  }

  $object = new $selClass;
  $list = $object->seek($keywords_search);
  foreach($list as $key => $value) {
    $list[$key]->loadRefsFwd();
    if(!$list[$key]->canRead()) {
      unset($list[$key]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

if($selClass){
  $smarty->assign("list"       , $list);
}
$smarty->assign("listClass"  , $listClass );
$smarty->assign("keywords"   , $keywords  );
$smarty->assign("selClass"   , $selClass  );
$smarty->assign("onlyclass"  , $onlyclass );
$smarty->display("object_selector.tpl");
?>
