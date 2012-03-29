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
    
    $this->_old->_ex_class_id = $_ex_class_id;
    $this->_old->setExClass();
    
    return parent::doBind();
  }
}

$do = new CDoExObjectAddEdit("CExObject");
$do->doIt();
