<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

// Source PMSI
$pmsi_source = CExchangeSource::get("pmsi", null, true);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pmsi_source" , $pmsi_source);

$smarty->display("configure.tpl");

?>