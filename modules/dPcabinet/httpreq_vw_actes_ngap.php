<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$object_id    = CValue::getOrSession("object_id");
$object_class = CValue::getOrSession("object_class");

// Chargement de la consultation
/** @var CCodable $object */
$object = new $object_class;
$object->load($object_id);
$object->loadRefsActesNGAP();
$object->loadRefPraticien();
$object->loadRefPatient();

$date            = CValue::getOrSession("date", CMbDT::date());
$date_now        = CMbDT::date();

// Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($object);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("acte_ngap"      , $acte_ngap);
$smarty->assign("object"         , $object);
$smarty->assign("subject"        , $object);
if ($object_class == "CConsultation") {
  $smarty->assign("_is_dentiste"   , $object->_is_dentiste);
}
elseif ($object_class == "COperation") {
  $object->loadRefChir();
  $smarty->assign("_is_dentiste"   , $object->_ref_chir->isDentiste());
}
else {
  $smarty->assign("_is_dentiste", false);
}

$smarty->display("inc_codage_ngap.tpl");