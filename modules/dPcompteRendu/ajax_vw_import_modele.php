<?php 

/**
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$owner_guid  = CView::request("owner_guid", "str");

CView::checkin();

$smarty = new CSmartyDP();

$smarty->assign("owner_guid", $owner_guid);

$smarty->display("inc_vw_import_modele.tpl");