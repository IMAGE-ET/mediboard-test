<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$operation_id = CValue::getOrSession("operation_id");

$date  = CValue::getOrSession("date", CMbDT::date());

$operation = new COperation();
$operation->load($operation_id);

//Chargement de la liste des praticiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

// Chargement de la liste des anesthesistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$operation->updateFormFields();
$operation->isCoded();
$operation->loadRefsActesCCAM();
$operation->loadExtCodesCCAM();
$operation->getAssociationCodesActes();
$operation->loadPossibleActes();
$operation->loadRefPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesths"      , $listAnesths );
$smarty->assign("listChirs"        , $listChirs   );
$smarty->assign("operation"        , $operation   );
$smarty->display("httpreq_codage_actes_reveil.tpl");
