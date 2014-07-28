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

$plage_id = CValue::get("plage_id");

$plage = new CPlageconsult();
$plage->load($plage_id);
$plage->loadRefsNotes();

$plage->loadRefChir()->loadRefFunction();
$plage->loadRefRemplacant()->loadRefFunction();

foreach ($plage->loadRefsConsultations() as $_consult) {
  $_consult->loadRefPatient()->loadRefPhotoIdentite();
}

$plage->loadDisponibilities();

// smarty
$smarty = new CSmartyDP();
$smarty->assign("object", $plage);;
$smarty->display("inc_vw_plage_consult.tpl");