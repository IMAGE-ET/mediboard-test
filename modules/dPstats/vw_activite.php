<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsEdit();

$debutact      = mbGetValueFromGetOrSession("debutact", mbDate());
$finact        = mbGetValueFromGetOrSession("finact", mbDate());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("debutact", $debutact);
$smarty->assign("finact"  , $finact);

$smarty->display("vw_activite.tpl");

?>