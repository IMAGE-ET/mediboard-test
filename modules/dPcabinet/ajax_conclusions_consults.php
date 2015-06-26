<?php 

/**
 * $Id$
 *  
 * @category Consultations
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$sejour_id  = CView::get("sejour_id", "num");
$consult_id = CView::get("consult_id", "num");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefsConsultations();

foreach ($sejour->_ref_consultations as $_consult) {
  if ($_consult->_id == $consult_id) {
    unset($sejour->_ref_consultations[$_consult->_id]);
    continue;
  }
  $_consult->loadRefPlageConsult();
}

$smarty = new CSmartyDP();

$smarty->assign("sejour"    , $sejour);
$smarty->assign("consult_id", $consult_id);

$smarty->display("inc_conclusions_consults.tpl");