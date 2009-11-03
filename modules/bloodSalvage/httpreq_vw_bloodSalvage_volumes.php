<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$blood_salvage_id = CValue::postOrSession("blood_salvage_id");

$blood_salvage = new CBloodSalvage();
if($blood_salvage_id){
  $blood_salvage->load($blood_salvage_id);
  $blood_salvage->loadRefs();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blood_salvage", $blood_salvage);

$smarty->display("inc_vw_cell_saver_volumes.tpl");

?>