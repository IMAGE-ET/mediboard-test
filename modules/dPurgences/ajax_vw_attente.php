<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

// Chargement du rpu
$rpu_id = CValue::getOrSession("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu"   , $rpu);
$smarty->display("inc_vw_attente.tpl");

?>