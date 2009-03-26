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

$message_hprim_id = mbGetValueFromGet("message_hprim_id");

// Chargement du message HPRIM demandé
$msg_hprim = new CMessageHprim();
$msg_hprim->load($message_hprim_id);
if($msg_hprim->load($message_hprim_id))
  $msg_hprim->loadRefs();

// Récupération de la liste des messages HPRIM
$itemMessageHprim = new CMessageHprim;
$where["initiateur_id"] = "IS NULL";
$listMessageHprim = $itemMessageHprim->loadList($where);
foreach($listMessageHprim as &$curr_msg_hprim) 
  $curr_msg_hprim->loadRefNotifications();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("msg_hprim"        , $msg_hprim);
$smarty->assign("listMessageHprim" , $listMessageHprim);
$smarty->display("vw_idx_message_hprim.tpl");
?>
