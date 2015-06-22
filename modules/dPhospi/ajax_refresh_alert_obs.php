<?php 

/**
 * $Id$
 *  
 * @category Hospitalisation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$obs_id = CView::get("obs_id", "num");

CView::checkin();

$obs = new CObservationMedicale();
$obs->load($obs_id);

$obs->loadRefAlerte();
$obs->_ref_alerte->loadRefHandledUser();

$smarty = new CSmartyDP();

$smarty->assign("obs", $obs);

$smarty->display("inc_vw_alerte_obs.tpl");