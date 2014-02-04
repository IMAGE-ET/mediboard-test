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

$domain_id = CValue::get("domain_id");

$domain = new CDomain();
$domain->load($domain_id);

$domain->countObjects();

$smarty = new CSmartyDP();
$smarty->assign("domain", $domain);
$smarty->display("inc_show_domain_details.tpl");