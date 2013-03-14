<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 */


$action = CValue::get("action", "null");

$CCDATools = new CCdaTools();
$result = "";
switch ($action) {
  case "createClass":
    $result = $CCDATools->createClass();
    break;

  case "createTest":
    $result = $CCDATools->createTestSchemaClasses();
    break;

  case "clearXSD":
    $result = $CCDATools->clearXSD();
    break;

  case "missClass";
    $result = $CCDATools->missclass();
    break;
}

$smarty = new CSmartyDP();

$smarty->assign("action", $action);
$smarty->assign("result", $result);

$smarty->display("vw_toolsdatatype.tpl");