<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$guid        = CValue::get("source_guid");
$source_name = CValue::get("source_name");
$object_guid = CValue::get("object_guid");
$light       = CValue::get("light", false);

/** @var CExchangeSource $source */
$source = CMbObject::loadFromGuid($guid);
$source->name = $source_name;

if ($source instanceof CSourcePOP && !$source->_id && $object_guid) {
  list($object_class, $object_id) = explode("-", $object_guid);

  /** @var CSourcePOP $source */
  $source->object_class = $object_class;
  $source->object_id    = $object_id;
}

$smarty = new CSmartyDP("modules/".$source->_ref_module->mod_name);
$smarty->assign("source", $source);
$smarty->assign("light" , $light);
$smarty->display($source->_class."_inc_config.tpl");