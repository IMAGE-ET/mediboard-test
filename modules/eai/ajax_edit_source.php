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

$smarty = new CSmartyDP("modules/ftp");
$smarty->assign("source", $source);
$smarty->display("CSourceFTP_inc_config.tpl");