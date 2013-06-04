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

CCanDo::checkEdit();

$consultation_id = CValue::get("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadView();
	$consult->loadComplete();
	$consult->loadRefsActesNGAP();
	$consult->loadRefPraticien();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("consult", $consult);
$smarty->display("print_actes.tpl");
