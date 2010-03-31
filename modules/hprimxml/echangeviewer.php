<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$echange_hprim_id = CValue::get("echange_hprim_id");

$echange_hprim = new CEchangeHprim();
$echange_hprim->load($echange_hprim_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("echange_hprim", $echange_hprim);
$smarty->display("echangeviewer.tpl");

?>