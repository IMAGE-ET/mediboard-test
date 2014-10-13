<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


$sejour_id = CValue::get("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);

// smarty
$smarty = new CSmartyDP();
$smarty->assign("bris", new CBrisDeGlace());
$smarty->assign("sejour", $sejour);
$smarty->display("inc_vw_form_bris_de_glace.tpl");