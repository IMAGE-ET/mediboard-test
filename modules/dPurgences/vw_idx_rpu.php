<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

CAppUI::requireModuleFile("dPurgences", "redirect_barcode");

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "_pec_transport");

// Type d'affichage main courante
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Type d'affichage UHCD
$uhcd_affichage = CValue::postOrSession("uhcd_affichage", CAppUI::conf("dPurgences default_view"));

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$today = mbDate();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("group"           , CGroups::loadCurrent());
$smarty->assign("selAffichage"    , $selAffichage);
$smarty->assign("uhcd_affichage"  , $uhcd_affichage);
$smarty->assign("date"            , $date);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_idx_rpu.tpl");
?>