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

$date            = CValue::getOrSession("date", mbDate());
$date_now        = mbDate();

// Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($object);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("acte_ngap"      , $acte_ngap);
$smarty->assign("object"         , $object);
$smarty->assign("subject"        , $object);

$smarty->display("inc_codage_ngap.tpl");
?>