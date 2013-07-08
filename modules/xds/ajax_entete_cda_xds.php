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

mbTrace($extrinsic);
$smarty = new CSmartyDP();

$smarty->assign("message", $message);

$smarty->display("inc_display_xds.tpl");