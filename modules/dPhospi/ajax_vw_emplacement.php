<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hospi
 * @version $Revision: 6518 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$chambre_id     = CValue::getOrSession("chambre_id");
$service_id     = CValue::getOrSession("service_id");

$emplacement = new CEmplacement();
$where = array();
$where["chambre_id"] = " = '$chambre_id'";
$emplacement->loadObject($where);

$service = new CService();
$service->load($service_id);
$service->loadRefsChambres();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("emplacement"     , $emplacement);
$smarty->assign("chambres"        , $service->_ref_chambres);

$smarty->display("inc_vw_emplacement.tpl");
?>