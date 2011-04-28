<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_class_id = CValue::get("ex_class_id");
$keywords = CValue::get("_host_field_view");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);
$ex_class->getAvailableFields();
//$ex_class->loadTargetObject();

$list = array();

foreach($ex_class->_host_class_fields as $_field => $_spec) {
	$element = array(
	  "prop"  => $_spec,
		"value" => $_field,
    "title" => null,
    "view"  => null,
    "type"  => null,
    "level" => 0,
	);
	
	$_subfield = explode(".", $_field);
	
	// Level 1 title
	if ($_spec instanceof CRefSpec && $_spec->class) {
    if ($_spec->meta) {
      $_meta_spec = $ex_class->_host_class_fields[$_spec->meta];
			$element["type"] = implode(" OU ", $_meta_spec->_locales);
    }
		else {
      $element["type"] = CAppUI::tr($_spec->class);
    }
  }
	else {
		$element["type"] = CAppUI::tr("CMbFieldSpec.type.".$_spec->getSpecType());
  }
	
	// Level 1 type
  if (count($_subfield) > 1) {
    $element["title"] = CAppUI::tr("$ex_class->host_class-$_subfield[0]")." de type ".CAppUI::tr("$_subfield[1]");
  }
	else {
		$element["title"] = CAppUI::tr("$ex_class->host_class-$_field");
  }
	
	$element["view"] = $element["title"];
	$parent_view = $element["view"];
	
	$list[] = $element;
	
	// Level 2
  if ($_spec instanceof CRefSpec) {
    foreach ($_spec->_subspecs as $_key => $_subspec) {
      $_subfield = explode(".", $_key);
      $_subfield = reset($_subfield);
			
	    $element = array(
	      "prop"  => $_subspec,
	      "value" => "$_field-$_key",
	      "title" => null,
	      "type"  => null,
				"level" => 1,
	    );
      
			if ($_subspec instanceof CRefSpec && $_subspec->class) {
	      if ($_subspec->meta) {
	        //$_meta_spec = $ex_class->_host_class_fields[$_spec->meta];
          //$element["type"] = implode(" OU ", $_meta_spec->_locales);
	      }
				else {
	        $element["type"] = CAppUI::tr("$_subspec->class");
	      }
	    }
			else {
	      $element["type"] = CAppUI::tr("CMbFieldSpec.type.".$_subspec->getSpecType());
	    }
      
			$element["view"] = $parent_view." / ".CAppUI::tr("$_subspec->className-$_subfield");
			$element["title"] = " |- ".CAppUI::tr("$_subspec->className-$_subfield");
			
      $list[] = $element;
    }
  }
}

$show_views = false;

// filtrage
if ($keywords) {
	$show_views = true;
	
	$re = preg_quote($keywords);
  $re = CMbString::allowDiacriticsInRegexp($re);
  $re = str_replace("/", "\\/", $re);
  $re = "/($re)/i";

	foreach($list as $_key => $element) {
		if (!preg_match($re, $element["title"])) {
			unset($list[$_key]);
		}
	}
}

$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->assign("host_fields", $list);
$smarty->assign("show_views", $show_views);
$smarty->display("inc_autocomplete_hostfields.tpl");
