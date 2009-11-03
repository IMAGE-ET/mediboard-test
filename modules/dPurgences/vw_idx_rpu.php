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
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", "ccmu");

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$today = mbDate();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date"        , $date);

$smarty->display("vw_idx_rpu.tpl");
?>