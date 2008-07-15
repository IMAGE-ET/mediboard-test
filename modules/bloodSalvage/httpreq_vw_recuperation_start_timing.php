<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision: $
* @author Alexandre Germonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$blood_salvage_id = mbGetValueFromPostOrSession("blood_salvage_id");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$modif_operation    = $date>=mbDate();

$blood_salvage = new CBloodSalvage();
if($blood_salvage_id){
  $blood_salvage->load($blood_salvage_id);
  $blood_salvage->loadRefs();
  


}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("blood_salvage"  , $blood_salvage  );
$smarty->assign("date"           , $date           );
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("timing", $timing);


$smarty->display("inc_vw_recuperation_start_timing.tpl");

?>