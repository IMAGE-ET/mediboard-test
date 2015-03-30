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

$patient_id = CView::get("patient_id", "num pos");
$tri        = CView::get("tri"       , "enum list|date|context|cat default|date");
$display    = CView::get("display"   , "enum list|icon|list default|icon");

CView::checkin();

$patient = new CPatient();
$patient->load($patient_id);

$patient->loadAllDocs($tri);

$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("display", $display);
$smarty->assign("tri"    , $tri);

$smarty->display("inc_all_docs.tpl");