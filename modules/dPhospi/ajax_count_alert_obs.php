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

$sejour_id = CView::get("sejour_id", "num");

CView::checkin();

$sejour = new CSejour();
$sejour->load($sejour_id);

echo $sejour->countAlertsNotHandled("medium", "observation");