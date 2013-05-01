<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkAdmin();

$pratId    = CValue::getOrSession("object_id");
$pratId400 = CValue::getOrSession("id400");
$date      = CMbDT::dateTime();

//Création d'un nouvel id400 pour le laboratoire
$new_idex = new CIdSante400();

$prat = new CMediusers();
$listPrat = $prat->loadPraticiens();

$remote_name = CAppUI::conf("dPlabo CCatalogueLabo remote_name");

$idex = new CIdSante400();
$idex->object_class = "CMediusers";
$idex->tag = $remote_name;
$order = "last_update DESC";

$idexs = $idex->loadMatchingList($order);

foreach ($idexs as $_idex) {
  $_idex->loadRefs();
}

$prescriptionlabo_source = CExchangeSource::get("prescriptionlabo", "ftp", true, null, false);
$get_id_prescriptionlabo_source = CExchangeSource::get("get_id_prescriptionlabo", "soap", true, null, false);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescriptionlabo_source" , $prescriptionlabo_source);
$smarty->assign("get_id_prescriptionlabo_source" , $get_id_prescriptionlabo_source);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("date", $date);
$smarty->assign("remote_name", $remote_name);
$smarty->assign("newId400", $new_idex);
$smarty->assign("list_idSante400", $idexs);

$smarty->display("configure.tpl");