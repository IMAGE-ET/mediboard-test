<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$group = CGroups::loadCurrent();
$cabinet_id = $group->service_urgences_id;

$consult = new CConsultation;
$consult->motif = CAppUI::tr("CConsultation.reconvoc_immediate");
$consult->_datetime = "now";

$praticiens = array();
$praticiens = CAppUI::pref("pratOnlyForConsult", 1) ? 
  CMediUsers::get()->loadPraticiens(PERM_READ, $cabinet_id) :
  CMediUsers::get()->loadProfessionnelDeSante(PERM_READ, $cabinet_id);

$smarty = new CSmartyDP;
$smarty->assign("praticiens", $praticiens);
$smarty->assign("consult"   , $consult);
$smarty->display("inc_create_reconvoc.tpl");
