<?php 

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$action = CValue::get("action", "null");
$resultat = false;
switch ($action) {
  case "createXml":
    $resultat = CXDSTools::generateXMLToJv();
    break;
}

$smarty = new CSmartyDP();
$smarty->assign("action"  , $action);
$smarty->assign("result", $resultat);
$smarty->display("vw_tools_xds.tpl");