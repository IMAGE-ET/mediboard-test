<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$sejour_id       = CValue::get("sejour_id");
$sejour_id_futur = CValue::get("sejour_id_futur");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour_merge = new CSejour();
$sejour_merge->load($sejour_id_futur);


$sejour_merge->entree_reelle  = $sejour->entree_reelle;
$sejour_merge->mode_entree_id = $sejour->mode_entree_id;
$sejour_merge->mode_entree    = $sejour->mode_entree;
$sejour_merge->provenance     = $sejour->provenance;

$msg = $sejour_merge->checkMerge(array($sejour));

$smarty = new CSmartyDP();
$smarty->assign("message", $msg);
$smarty->display("inc_result_check_merge.tpl");