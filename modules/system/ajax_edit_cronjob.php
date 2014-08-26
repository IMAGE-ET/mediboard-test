<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$identifiant = CValue::get("identifiant");
$list_ip     = trim(CAppUI::conf("servers_ip"));
$address     = array();

if ($list_ip) {
  $address = preg_split("/\s*,\s*/", $list_ip, -1, PREG_SPLIT_NO_EMPTY);
}

$cronjob = new CCronJob();
$cronjob->load($identifiant);

$smarty = new CSmartyDP();
$smarty->assign("cronjob", $cronjob);
$smarty->assign("address", $address);
$smarty->display("inc_edit_cronjob.tpl");