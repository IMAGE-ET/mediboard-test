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

$dossier_anesth_id = CValue::get("dossier_anesth_id");

$consult_anesth = new CConsultAnesth();
$consult_anesth->load($dossier_anesth_id);
$consult = $consult_anesth->loadRefConsultation();

$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult);
$smarty->assign("consult_anesth", $consult_anesth);

$smarty->display("inc_consult_anesth/acc_examens_clinique.tpl");