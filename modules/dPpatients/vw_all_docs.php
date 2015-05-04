<?php 

/**
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$patient_id   = CView::get("patient_id"  , "num pos");
$context_guid = CView::get("context_guid", "str default|CPatient-$patient_id");
$tri          = CView::get("tri"         , "enum list|date|context|cat default|date");
$display      = CView::get("display"     , "enum list|icon|list default|icon");

CView::checkin();

$smarty = new CSmartyDP();

$smarty->assign("patient_id"  , $patient_id);
$smarty->assign("context_guid", $context_guid);
$smarty->assign("display"     , $display);
$smarty->assign("tri"         , $tri);

$smarty->display("vw_all_docs.tpl");