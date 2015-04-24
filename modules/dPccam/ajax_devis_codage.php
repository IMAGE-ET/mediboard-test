<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$object_id = CValue::get('object_id');
$object_class = CValue::get('object_class');

/** @var CCodable $object */
$object = CMbObject::loadFromGuid("$object_class-$object_id");
$object->loadRefPraticien();

$devis = new CDevisCodage();
$devis->codable_class = $object->_class;
$devis->codable_id = $object->_id;
$devis->loadMatchingObject();

if (!$devis->_id) {
  $devis->event_type = $object->_class;
  $devis->patient_id = $object->loadRefPatient()->_id;
  $devis->praticien_id = $object->loadRefPraticien()->_id;
  if ($object->_class == 'CConsultation') {
    $devis->libelle = $object->motif;
    $object->loadRefPlageConsult();
    $devis->date = $object->_date;
  }
  elseif ($object->_class == 'COperation') {
    $devis->libelle = $object->libelle;
    $devis->date = $object->date;
  }
  $devis->codes_ccam = $object->codes_ccam;
}

$smarty = new CSmartyDP();
$smarty->assign('devis', $devis);
$smarty->display('inc_devis_codage.tpl');