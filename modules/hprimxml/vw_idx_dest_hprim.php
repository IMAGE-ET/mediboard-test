<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkRead();

$dest_hprim_id = CValue::get("dest_hprim_id");

$listEtab = CGroups::loadGroups();

// Chargement du destinataire HPRIM demandé
$dest_hprim = new CDestinataireHprim();
$dest_hprim->load($dest_hprim_id);
if($dest_hprim->load($dest_hprim_id)) {
  $dest_hprim->loadRefsFwd();
	$dest_hprim->loadRefsExchangesSources();
}

// Récupération de la liste des destinataires HPRIM
$itemDestHprim = new CDestinataireHprim;
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$listDestHprim = $itemDestHprim->loadList($where);
foreach($listDestHprim as &$_dest_hprim) {
  $_dest_hprim->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dest_hprim"      , $dest_hprim);
$smarty->assign("listEtab"        , $listEtab);
$smarty->assign("listDestHprim"   , $listDestHprim);
$smarty->display("vw_idx_dest_hprim.tpl");
?>