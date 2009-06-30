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

$sejour_id = mbGetValueFromGet("sejour_id");
$rpu_id    = mbGetValueFromGet("rpu_id");

$now = mbDateTime();

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefRPU();
$rpu = $sejour->_ref_rpu;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("now"   , $now);

$smarty->assign("sejour", $sejour);
$smarty->assign("rpu"   , $rpu);

$smarty->display("inc_sortie_prevue.tpl");
?>