<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Acte CCAM controller
 */
class CDoActeCCAMAddEdit extends CDoObjectAddEdit {
  /** @var CMbObject */
  public $_ref_object;

  /**
   * Constructor
   */
  function CDoActeCCAMAddEdit() {
    $this->CDoObjectAddEdit("CActeCCAM", "acte_id");
  }

  /**
   * @see parent::doBind()
   */
  function doBind() {
    parent::doBind();
    $this->_obj->modificateurs = "";
    $listModifConvergence = array("X", "I", "9", "O");
    foreach ($_POST as $propName => $propValue) {
      $matches = null;
      if (preg_match("/modificateur_(.)(.)/", $propName, $matches)) {
        $modificateur = $matches[1];
        if (strpos($this->_obj->modificateurs, $matches[1]) === false) {
          $this->_obj->modificateurs .= $modificateur;
          if ($matches[2] == 2) {
            $this->_obj->modificateurs .= $modificateur;
          }
        }
      }
    }
    $this->_obj->loadRefObject();
    $this->_ref_object = $this->_obj->_ref_object;
  }

  /**
   * @see parent::doRedirect()
   */
  function doRedirect() {
    if (CAppUI::conf("dPsalleOp CActeCCAM codage_strict") || !$this->_old->_id || !$this->_obj->_id) {
      $this->_ref_object->correctActes();
    }
    parent::doRedirect();
  }
}

$do = new CDoActeCCAMAddEdit();
$do->doIt();
