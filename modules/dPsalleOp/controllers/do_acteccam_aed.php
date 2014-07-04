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
    if ($this->_obj->_edit_modificateurs) {
      $this->_obj->modificateurs = "";
      $dents = array();
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
        $matches = null;
        if (preg_match("/dent_(.{1,2})/", $propName, $matches)) {
          $dents[] = $matches[1];
        }
      }
      $this->_obj->position_dentaire = implode("|", $dents);
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
