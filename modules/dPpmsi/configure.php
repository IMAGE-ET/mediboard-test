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
$exchange_source = CExchangeSource::get("pmsi");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchange_objects", CExchangeSource::getObjects());
$smarty->assign("exchange_source" , $exchange_source);
$smarty->display("configure.tpl");

?>