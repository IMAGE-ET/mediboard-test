<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Etablissement courant
$group = CGroups::loadCurrent();
$date = CValue::getOrSession("date", CMbDT::date());
$readonly = CValue::get("readonly", false);

// Plateaux disponibles
$plateau = new CPlateauTechnique;
$plateau->group_id = $group->_id;
$plateau->repartition = "1";
/** @var CPlateauTechnique[] $plateaux */
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $_plateau->loadRefsTechniciens();
  foreach ($_plateau->_ref_techniciens as $_technicien) {
    $_technicien->loadRefCongeDate($date);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("plateaux", $plateaux);
$smarty->assign("bilan", new CBilanSSR);
$smarty->assign("readonly", $readonly);
$smarty->display("vw_idx_repartition.tpl");
