<?php 

/**
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$medecin = new CMedecin();
$medecin->load(CValue::get("medecin_id"));

// smarty
$smarty = new CSmartyDP();
$smarty->assign("object", $medecin);
$smarty->display("inc_edit_medecin.tpl");