<?php 

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_id    = CValue::getOrSession("object_id");
$object_class = CValue::getOrSession("object_class");

// Chargement de la consultation
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
if ($object_class = "CConsultation") {
  $smarty->assign("_is_dentiste"   , $object->_is_dentiste);
}

$smarty->display("inc_codage_ngap.tpl");