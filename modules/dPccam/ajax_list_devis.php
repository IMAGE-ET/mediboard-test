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

$object_id    = CValue::get('object_id');
$object_class = CValue::get('object_class');

$object = CMbObject::loadFromGuid("$object_class-$object_id");
$object->loadRefPraticien();
$object->loadRefPatient();
$list_devis = $object->loadBackRefs('devis_codage', 'creation_date ASC', null, 'devis_codage_id');

foreach ($list_devis as $_devis) {
  $_devis->updateFormFields();
  $_devis->countActes();
}

$smarty = new CSmartyDP();
$smarty->assign('object', $object);
$smarty->assign('list_devis', $list_devis);
$smarty->display('inc_list_devis.tpl');