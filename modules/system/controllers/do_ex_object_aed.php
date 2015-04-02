<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Custom controller for CExObject
 *
 * @property CExObject _obj
 * @property CExObject _old
 */
class CDoExObjectAddEdit extends CDoObjectAddEdit {
  /**
   * @see parent::onAfterInstanciation()
   */
  function onAfterInstanciation(){
    $_ex_class_id = CValue::read($this->request, "_ex_class_id");

    $this->_obj->setExClass($_ex_class_id);
    $this->_old->setExClass($_ex_class_id);
  }

  function doStore() {
    parent::doStore();

    if (CModule::getActive("dPprescription") && !$this->_old->_id) {
      $p_to_c = new CPrescriptionProtocoleToConcept();
      $count_p_to_c = $p_to_c->countList();

      if ($count_p_to_c > 0) {
        /** @var CExObject $ex_object */
        $ex_object = $this->_obj;

        $all_fields = $ex_object->loadRefExClass()->loadRefsAllFields();
        $bool_concept_ids = array();
        foreach ($all_fields as $_field) {
          if (strpos($_field->prop, "bool") === 0 && $_field->concept_id && $ex_object->{$_field->name} == "1") {
            $bool_concept_ids[] = $_field->concept_id;
          }
        }

        $bool_concept_ids = array_unique($bool_concept_ids);

        $where = array(
          "concept_id" => $p_to_c->getDS()->prepareIn($bool_concept_ids)
        );
        $protocole_ids = array_values(CMbArray::pluck($p_to_c->loadList($where), "protocole_id"));

        if (count($protocole_ids) && CMediusers::get()->isPraticien()) {
          /** @var CSejour $sejour */
          $sejour = $ex_object->getReferenceObject("CSejour");
          if ($sejour && $sejour->_id) {
            $prescription = $sejour->loadRefPrescriptionSejour();

            if (!$prescription->_id) {
              $prescription = new CPrescription();
              $prescription->object_id = $sejour->_id;
              $prescription->object_class = $sejour->_class;
              $prescription->type = "sejour";

              if ($msg = $prescription->store()) {
                CAppUI::setMsg($msg, UI_MSG_WARNING);
              }
            }
            $ops_ids = implode("-", CMbArray::pluck($sejour->loadRefsOperations(array("annulee" => "= '0'")), "operation_id"));
            CAppUI::callbackAjax("window.opener.ExObject.checkOpsBeforeProtocole", $protocole_ids, $prescription->_id, $sejour->_id, $ops_ids);
          }
        }
      }
    }
  }
}

$do = new CDoExObjectAddEdit("CExObject");
$do->doIt();
