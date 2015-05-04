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

$object_class = CView::get("object_class", "str");
$object_id    = CView::get("object_id", "num pos");
$patient_id   = CView::get("patient_id", "num pos");
$praticien_id = CView::get("praticien_id", "num pos");

CView::checkin();

$object = new $object_class;
$object->load($object_id);

$object->countDocItems();

$user = CMediusers::get();

// Praticien concerné
if (!$user->isPraticien() && $praticien_id) {
  $user = new CMediusers();
  $user->load($praticien_id);
}

$user->loadRefFunction();
$user->_ref_function->loadRefGroup();
$user->canDo();

$compte_rendu = new CCompteRendu();

$smarty = new CSmartyDP();

$smarty->assign("object"               , $object);
$smarty->assign("praticien"            , $user);
$smarty->assign("patient_id"           , $patient_id);

$smarty->display("inc_widget_count_documents.tpl");