<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Integer value (zerofilled)
 */
class CNumcharSpec extends CNumSpec {
  public $control;
  public $protected;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "numchar";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    $type_sql = "BIGINT ZEROFILL";
    
    if ($this->maxLength || $this->length) {
      $length = $this->maxLength ? $this->maxLength : $this->length;
      $valeur_max = pow(10, $length);
      
      $type_sql = "TINYINT";
      
      if ($valeur_max > pow(2, 8)) {
        $type_sql = "MEDIUMINT";
      }
      if ($valeur_max > pow(2, 16)) {
        $type_sql = "INT";
      }
      if ($valeur_max > pow(2, 32)) {
        $type_sql = "BIGINT";
      }
      
      $type_sql .= "($length) UNSIGNED ZEROFILL";
    }
    
    return $type_sql;
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'control'   => 'str',
      'protected' => 'bool',
    ) + parent::getOptions();
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
        
    // control
    if ($this->control) {
      // Luhn control
      if ($this->control == "luhn" && !luhn($propValue)) {
        return "La clé est incorrecte";
      }
    }

    return null;
  }
}
