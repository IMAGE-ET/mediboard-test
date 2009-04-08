<?php /* $Id: $ */
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;

$can->needsRead();

$dest_hprim_id = mbGetValueFromGetOrSession("dest_hprim_id");

// Chargement du destinataire HPRIM demandé
$dest_hprim = new CDestinataireHprim();
$dest_hprim->load($dest_hprim_id);
if($dest_hprim->load($dest_hprim_id))
  $dest_hprim->loadRefs();

// Récupération de la liste des destinataires HPRIM
$itemDestHprim = new CDestinataireHprim;
$listDestHprim = $itemDestHprim->loadList(null);
foreach($listDestHprim as &$curr_dest_hprim) 
  $curr_dest_hprim->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dest_hprim"    , $dest_hprim);
$smarty->assign("listDestHprim" , $listDestHprim);
$smarty->display("vw_idx_dest_hprim.tpl");
?>
