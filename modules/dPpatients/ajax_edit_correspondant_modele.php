<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$correspondant_id = CValue::get("correspondant_id");

$correspondant = new CCorrespondantModele();
$correspondant->load($correspondant_id);

$smarty = new CSmartyDP();

$smarty->assign("correspondant", $correspondant);
$smarty->assign("mode_modele"  , 1);

$smarty->display("inc_form_correspondant.tpl");
