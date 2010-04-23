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

// Type d'affichage
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$today = mbDate();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("group"       , CGroups::loadCurrent());
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date"        , $date);
$smarty->assign("isImedsInstalled"  , CModule::getActive("dPImeds"));

$smarty->display("vw_idx_rpu.tpl");
?>