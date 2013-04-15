<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
CCanDo::checkRead();
$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->_id = $consult_id;
$consult->load();
$consult->loadRefPatient();
$consult->loadRefGrossesse();

$type = "";
switch ($consult->type_assurance) {
  case "classique" :
    $type = "assurance_classique";
    break;

  case "at" :
    $type = "accident_travail";
    break;

  case "smg" :
    $type = "soins_medicaux_gratuits";
    break;

  case "maternite" :
    $type = "maternite";
    break;
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("consult", $consult);
$smarty->display("inc_type_assurance_reglement/$type.tpl");