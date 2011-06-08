<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");
$smarty->assign("consult" , $consult);
$smarty->assign("readonly", 1);
$smarty->display("inc_main_consultform.tpl");

?>