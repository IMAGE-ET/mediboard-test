<?php

/**
 * Liste déroulante des depend values des aides à a saisie
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object_class = CValue::get("object_class");
$field        = CValue::get("field");

$object = new $object_class;
$list = array();

if ($object->_specs[$field] instanceof CEnumSpec) {
  $list = $object->_specs[$field]->_locales;
}

array_unshift($list, " - ".CAppUI::tr("None"));

$smarty = new CSmartyDP();

$smarty->assign("list", $list);

$smarty->display("inc_select_enum_values.tpl");
