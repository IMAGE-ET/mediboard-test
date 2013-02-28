<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();
// Période
$filter = new CPlageconsult();
$filter->_date_min  = CValue::getOrSession("_date_min");
$filter->_date_max  = CValue::getOrSession("_date_max");

// Tri sur les praticiens
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();

$prat = new CMediusers;
$prat->load(CValue::getOrSession("chir"));
if (!$prat->_id) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CMediusers-warning-undefined");
  return;
}
$prat->loadRefFunction();
$listPrat = array($prat->_id => $prat);

$plageconsult = new CPlageconsult();
$ljoin = array();
$ljoin["consultation"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

$where = array();

$where[] = "
  (plageconsult.chir_id  <> '$prat->_id' AND 
    (plageconsult.remplacant_id = '$prat->_id' OR plageconsult.pour_compte_id = '$prat->_id'))
  OR 
  (plageconsult.chir_id  = '$prat->_id' AND 
    ((plageconsult.remplacant_id <> '$prat->_id' AND plageconsult.remplacant_id IS NOT NULL)
      OR 
     (plageconsult.pour_compte_id <> '$prat->_id' AND plageconsult.pour_compte_id IS NOT NULL))
   )";

$where["plageconsult.date"] = " BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
$where["consultation.annule"] = "= '0'";
$order = "chir_id ASC";

$listPlages = $plageconsult->loadList($where, $order, null, null, $ljoin);

$plages = array();
foreach ($listPlages as $plage) {
  $plage->loadRefsConsultations();
  $plages[$plage->_id]["total"] = 0;
  foreach ($plage->_ref_consultations as $consult) {
    $consult->loadRefPatient();
    $plages[$plage->_id]["total"] += $consult->du_patient * $plage->pct_retrocession/100; 
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPrat"    , $listPrat);
$smarty->assign("listPlages"  , $listPlages);
$smarty->assign("filter"      , $filter);
$smarty->assign("plages"      , $plages);

$smarty->display("print_retrocession.tpl");
?>