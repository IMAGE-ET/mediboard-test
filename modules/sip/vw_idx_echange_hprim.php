<?php /* $Id: $ */
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;

$can->needsRead();

$echange_hprim_id = mbGetValueFromGet("echange_hprim_id");

// Chargement du message HPRIM demandé
$echange_hprim = new CEchangeHprim();
$echange_hprim->load($echange_hprim_id);
if($echange_hprim->load($echange_hprim_id))
  $echange_hprim->loadRefs();

// Récupération de la liste des messages HPRIM
$itemMessageHprim = new CEchangeHprim;
$where["initiateur_id"] = "IS NULL";
$listMessageHprim = $itemMessageHprim->loadList($where);
foreach($listMessageHprim as &$curr_echange_hprim) 
  $curr_echange_hprim->loadRefNotifications();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("echange_hprim"    , $echange_hprim);
$smarty->assign("listMessageHprim" , $listMessageHprim);
$smarty->display("vw_idx_echange_hprim.tpl");
?>
