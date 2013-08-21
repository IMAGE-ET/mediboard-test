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

$message = stripslashes(CValue::post("message"));
$xds = new CXDSMappingCDA($message);
$extrinsic = $xds->createExtrinsicObject("test");

$smarty = new CSmartyDP();

$smarty->assign("xds", CMbString::highlightCode("xml", $extrinsic->toXML()->saveXML()));

$smarty->display("inc_display_xds.tpl");