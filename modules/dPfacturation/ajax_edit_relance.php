<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
CCanDo::checkEdit();
$relance_id = CValue::get("relance_id");

$relance = new CRelance();
$relance->load($relance_id);

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("relance" , $relance);

$smarty->display("vw_edit_relance.tpl");