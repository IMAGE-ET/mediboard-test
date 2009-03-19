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

$cip_id = mbGetValueFromGetOrSession("cip_id");

// Chargement du CIP demandé
$cip = new CCip();
$cip->load($cip_id);
if($cip->load($cip_id))
  $cip->loadRefs();

// Récupération de la liste des CIPs
$itemCIP = new CCip;
$listCIP = $itemCIP->loadList(null);
foreach($listCIP as &$curr_cip) 
  $curr_cip->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("cip", $cip);
$smarty->assign("listCIP", $listCIP);
$smarty->display("vw_idx_cip.tpl");
?>
