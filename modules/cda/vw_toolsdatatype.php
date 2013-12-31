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

CCanDo::checkAdmin();

$action = CValue::get("action", "null");
$result = "";

switch ($action) {
  case "createClass":
    $result = CCdaTools::createClass();
    break;

  case "createTest":
    $result = CCdaTools::createAllTestSchemaClasses();
    break;

  case "clearXSD":
    $result = CCdaTools::clearXSD();
    break;

  case "missClass":
    $result = CCdaTools::missclass();
    break;

  case "createClassXSD":
    $result = CCdaTools::createClassFromXSD();
    break;
}

$smarty = new CSmartyDP();

$smarty->assign("action", $action);
$smarty->assign("result", $result);

$smarty->display("vw_toolsdatatype.tpl");