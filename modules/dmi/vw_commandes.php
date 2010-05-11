<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::get("date", mbDate());
$tomorow = mbDate("+1 DAY", $date);

$DMI = new CPrescriptionLineDMI();
$DMI->date = $date;

$where = array();
$where["date"] = "BETWEEN '$date' AND '$tomorow'";

$listDMI = $DMI->loadList($where, "date");

foreach($listDMI as $_dmi) {
  $_dmi->loadRefsFwd();
  $_dmi->_ref_prescription->loadRefPatient();
  $_dmi->_ref_product->loadBackRefs("references");
  $_dmi->_ref_product->loadRefStock();
  foreach($_dmi->_ref_product->_back["references"] as $_reference) {
  	$_reference->loadRefSociete();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date"   , $date);
$smarty->assign("DMI"    , $DMI);
$smarty->assign("listDMI", $listDMI);
$smarty->display("vw_commandes.tpl");
