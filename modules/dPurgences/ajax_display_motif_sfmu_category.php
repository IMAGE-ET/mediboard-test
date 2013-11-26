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

$categorie = CValue::get("categorie");
$list_motif_sfmu = array();

if ($categorie) {
  $motif_sfmu = new CMotifSFMU();
  $list_motif_sfmu = $motif_sfmu->loadList(array("categorie" => "='$categorie'"), null, null, "motif_sfmu_id");
}

$smarty = new CSmartyDP();
$smarty->assign("list_motif_sfmu", $list_motif_sfmu);
$smarty->display("inc_display_motif_sfmu_category.tpl");