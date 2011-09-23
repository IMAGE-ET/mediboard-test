<?php

/**
 * Table discipline médico-tarifaire
 *  
 * @category dPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CDisciplineTarifaire 
 * Table discipline médico-tarifaire
 */
class CDisciplineTarifaire extends CMbObject { 
   // DB Table key
  var $nodess      = null;
  var $description = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn   = 'discipline_tarifaire';
    $spec->table = "discipline_tarifaire";
    $spec->key   = "nodess";
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["nodess"]      = "num notNull maxLength|3";
    $props["description"] = "str";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"] = "CSejour discipline_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->description;
    $this->_shortview = $this->nodess;
  }
}
?>