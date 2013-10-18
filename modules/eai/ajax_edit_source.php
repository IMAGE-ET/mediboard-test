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

$guid   = CValue::get("source_guid");
$source = CMbObject::loadFromGuid($guid);

$smarty = new CSmartyDP("modules/".$source->_ref_module->mod_name);
$smarty->assign("source", $source);
$smarty->display($source->_class."_inc_config.tpl");