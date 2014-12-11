<?php 

/**
 * $Id$
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$grossesse_id = CValue::get("grossesse_id");

$grossesse = new CGrossesse();
$grossesse->load($grossesse_id);

$grossesse->loadRefsSejours();
$grossesse->loadRefsConsultations(true);

foreach ($grossesse->_ref_consultations as $_cons) {
  $prat = $_cons->loadRefPraticien();
  $prat->loadRefFunction();
}

foreach ($grossesse->_ref_sejours as $_sejour) {
  $_sejour->loadRefsConsultations();
  $_sejour->loadRefsOperations();
}

$smarty = new CSmartyDP();
$smarty->assign("grossesse", $grossesse);
$smarty->display("inc_list_grossesse_history.tpl");