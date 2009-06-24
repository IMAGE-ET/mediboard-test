<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m;

$can->needsRead();

// Type d'affichage
$selAffichage = mbGetValueFromPostOrSession("selAffichage","tous");

// Parametre de tri
$order_way = mbGetValueFromGetOrSession("order_way", "DESC");
$order_col = mbGetValueFromGetOrSession("order_col", "ccmu");

// Selection de la date
$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date"        , $date);

$smarty->display("vw_idx_rpu.tpl");
?>