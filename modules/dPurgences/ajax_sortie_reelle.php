<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$sejour_id  = CValue::get("sejour_id");
$consult_id = CValue::get("consult_id");

$now = mbDateTime();

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
?>