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
$user = CMediusers::get();
$group = CGroups::loadCurrent();

$rpu_id = CValue::get("rpu_id");

$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

$sejour = $rpu->_ref_sejour;
$sejour->_ref_rpu = $rpu;
$sejour->loadRefsFwd();
$sejour->_ref_rpu->loadRefSejourMutation();
$sejour->loadNDA();
$sejour->loadRefsConsultations();
$sejour->_ref_rpu->_ref_consult->loadRefsActes();
// Chargement de l'IPP
$sejour->_ref_patient->loadIPP();

// Chargement des services
$where = array();
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// Contraintes sur le mode de sortie / destination
$contrainteDestination["mutation"]  = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

// Praticiens urgentistes
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

// Si accès au module PMSI : peut modifier le diagnostic principal
$access_pmsi = 0;
if (CModule::exists("dPpmsi")) {
  $module = new CModule;
  $module->mod_name = "dPpmsi";
  $module->loadMatchingObject();
  $access_pmsi = $module->getPerm(PERM_EDIT);
}

// Si praticien : peut modifier le CCMU, GEMSA et diagnostic principal
$is_praticien = CAppUI::$user->isPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);
$smarty->assign("services", $services);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("sejour" , $sejour);
$smarty->assign("access_pmsi", $access_pmsi);
$smarty->assign("is_praticien", $is_praticien);
$smarty->assign("date" , CValue::getOrSession("date", CMbDT::date()));

$smarty->display("inc_sortie_rpu.tpl");
