<?php /* $Id: vw_idx_aides.php 8576 2010-04-15 12:35:57Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 8576 $
* @author Thomas Despoix
*/

global $can;
$can->needsRead();

$object_class = CValue::get("object_class");
$field        = CValue::get("field");

$object = new $object_class;

if ($object->_specs[$field] instanceof CEnumSpec)
  $list = $object->_specs[$field]->_locales;
else
  $list = array();
  
array_unshift($list, " - ".CAppUI::tr("None"));

$smarty = new CSmartyDP();
$smarty->assign("list", $list);
$smarty->display("inc_select_enum_values.tpl");
