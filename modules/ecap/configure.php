<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

CMedicap::getTag();

$ecap_files_source = CExchangeSource::get("ecap_files", "soap", true);
$ecap_ssr_source = CExchangeSource::get("ecap_ssr", "soap", true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("paths", CMedicap::$paths);
$smarty->assign("tags", CMedicap::$tags);
$smarty->assign("ecap_files_source" , $ecap_files_source);
$smarty->assign("ecap_ssr_source" , $ecap_ssr_source);
$smarty->display("configure.tpl");

?>