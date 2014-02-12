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
}

$do = new CDoExObjectAddEdit("CExObject");
$do->doIt();
