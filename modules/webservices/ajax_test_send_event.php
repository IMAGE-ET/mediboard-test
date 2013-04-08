<?php
/**
 * Test send event
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$function             = CValue::get("function");
$exchange_source_guid = CValue::get("exchange_source_guid");

preg_match('/^(\w+)\s+(\w+)\s*\(\s*(.*)\s*\)$/', $function, $matches);
$method = $matches[2];

preg_match_all('/(?:\,?\s*(\w+)\s+([\$\w]+))+?/', $matches[3], $matches);
$parameters = $matches;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("method"    , $method);
$smarty->assign("parameters", $parameters);
$smarty->assign("exchange_source_guid", $exchange_source_guid);
$smarty->display("inc_test_send_event.tpl");