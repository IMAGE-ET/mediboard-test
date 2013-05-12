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

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");
$smarty->assign("consult" , $consult);
$smarty->assign("readonly", 1);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->display("inc_main_consultform.tpl");
