<?php 

/**
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$subject_guid = CValue::get("subject_guid");
$read_only    = CValue::getOrSession("read_only", 0);

$prat = new CMediusers();
$listPrats = $prat->loadPraticiens();

$subject = CMbObject::loadFromGuid($subject_guid);

$subject->loadRefsActes();
$subject->loadExtCodesCCAM();
$subject->getAssociationCodesActes();
$subject->loadPossibleActes();

$smarty = new CSmartyDP();

$smarty->assign("subject"  , $subject);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("read_only", $read_only);

$smarty->display("inc_actes_ccam.tpl");