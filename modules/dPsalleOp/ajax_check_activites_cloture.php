<?php 

/**
 * Vérification d'activités 4 sur les actes pour la suppression d'actes non cotés avant envoi
 *  
 * @category dPsalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
$object->load($object_id);

$actes = explode("|", $object->codes_ccam);
$object->loadRefsActes();

$activites = CMbArray::pluck($object->_ref_actes, "code_activite");
$activite_4 = array_search("4", $activites);

$completed_activite_4 = 1;

foreach ($actes as $_acte) {
  $acte = CCodeCCAM::get($_acte);

  if (isset($acte->activites["4"]) && $activite_4 === false) {
    $completed_activite_4 = 0;
    break;
  }
}

echo json_encode($completed_activite_4);