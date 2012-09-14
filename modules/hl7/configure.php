<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$hl7v2_source = CExchangeSource::get("hl7v2", "ftp", true, null, false);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hl7v2_source" , $hl7v2_source);
$smarty->display("configure.tpl");

?>