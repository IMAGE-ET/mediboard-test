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

$sejour_id  = CValue::get("sejour_id");
$consult_id = CValue::get("consult_id");

$now = CMbDT::dateTime();

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefRPU();
$rpu = $sejour->_ref_rpu;

$consult = new CConsultation();
$consult->load($consult_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("now"   , $now);

$smarty->assign("sejour", $sejour);
$smarty->assign("consult", $consult);
$smarty->assign("rpu"   , $rpu);

$smarty->display("inc_sortie_reelle.tpl");
