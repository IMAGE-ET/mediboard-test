<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$consult_id = CValue::get("consult_id");
$dossier_anesth_id = CValue::get("dossier_anesth_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefConsultAnesth($dossier_anesth_id);
$consult->loadRefPlageConsult();

$smarty = new CSmartyDP();

$smarty->assign("consult", $consult);

$smarty->display("inc_fdr_consult.tpl");