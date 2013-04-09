<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCellSaver extends CMbObject {
  public $cell_saver_id;

  //DB Fields
  public $marque;
  public $modele;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cell_saver';
    $spec->key   = 'cell_saver_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["marque"] = "str notNull maxLength|50";
    $props["modele"] = "str notNull maxLength|50";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["blood_salvages"] = "CBloodSalvage cell_saver_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->marque $this->modele";
  }
}
