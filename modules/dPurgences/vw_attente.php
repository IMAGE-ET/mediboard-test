<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Selection de la date
$date = CValue::getOrSession("date", CMbDT::date());
$today = CMbDT::date();

// Chargement des urgences prises en charge
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
  
$where["entree"] = "LIKE '$date%'";
$where["type"] = "= 'urg'";
$where[] = "(rpu.radio_debut IS NOT NULL) OR (rpu.bio_depart IS NOT NULL) OR (rpu.specia_att IS NOT NULL)";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, null, null, null, $ljoin);

foreach ($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->_ref_rpu->loadRefSejourMutation();
  $_sejour->loadNDA();
  
  // Chargement de l'IPP
  $_sejour->_ref_patient->loadIPP();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listSejours"     , $listSejours);
$smarty->assign("date"            , $date);
$smarty->assign("today"           , $today);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_attente.tpl");
