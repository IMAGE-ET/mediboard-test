<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Yohann / Alexis
*/

global $AppUI, $can, $m;

$can->needsRead();

$selClass = mbGetValueFromGetOrSession("selClass", null);

$classSelected = array();

// Liste des Class
$listClass = getInstalledClasses();

$class_exist = array_search($selClass, $listClass);
if($class_exist === false){
  $selClass = null;
  mbSetValueToSession("selClass", $selClass);
  $classSelected =& $listClass;
}else{
  $classSelected[] = $selClass;
}

$backSpecs = array();
$backRefs = array();

//Extraction des propriétés 'spec'
foreach($classSelected as $selected) {
	$object = new $selected;
    $backRefs[$selected]=$object->_backRefs;	
	foreach ($object->_specs as $objetRefSpec) {
		if (is_a($objetRefSpec, 'CRefSpec')) {
	      $spec = array();
	      $spec[] = $objetRefSpec->className;
	      $spec[] = $objetRefSpec->fieldName;
	      if ($objetRefSpec->meta) {
	      	$spec[] = $objetRefSpec->meta;
	      }
		  $backSpecs[$objetRefSpec->class][] = join($spec, " ");
   		}
	}
}
mbTrace($backRefs);
mbTrace($backSpecs);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selClass"  , $selClass);
$smarty->assign("listClass" , $listClass);
$smarty->assign("backSpecs" , $backSpecs);
$smarty->assign("backRefs" , $backRefs);
$smarty->display("mnt_backref_classes.tpl");

?>