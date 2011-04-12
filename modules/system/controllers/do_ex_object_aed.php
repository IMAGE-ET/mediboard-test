<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDoExObjectAddEdit extends CDoObjectAddEdit {
  function doBind() {
    $_ex_class_id = CValue::read($this->request, "_ex_class_id");
    
    $this->_obj->_ex_class_id = $_ex_class_id;
    $this->_obj->setExClass();
    
    $this->_objBefore->_ex_class_id = $_ex_class_id;
    $this->_objBefore->setExClass();
    
    return parent::doBind();
  }
}

$do = new CDoExObjectAddEdit("CExObject");
$do->doIt();
