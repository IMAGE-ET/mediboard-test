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

$context_guid = CView::get("context_guid", "str");
$tri          = CView::get("tri"         , "enum list|date|context|cat default|date");
$display      = CView::get("display"     , "enum list|icon|list default|icon");

CView::checkin();

$context = CMbObject::loadFromGuid($context_guid);

$context->loadAllDocs($tri);

$smarty = new CSmartyDP();

$smarty->assign("context"   , $context);
$smarty->assign("display"   , $display);
$smarty->assign("tri"       , $tri);

$smarty->display("inc_all_docs.tpl");