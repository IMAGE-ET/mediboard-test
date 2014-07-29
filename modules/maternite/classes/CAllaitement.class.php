<?php

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CAllaitement extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $allaitement_id;

  // DB Fields
  public $patient_id;
  public $grossesse_id;
  public $date_debut;
  public $date_fin;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "allaitement";
    $spec->key    = "allaitement_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["patient_id"]   = "ref notNull class|CPatient";
    $props["grossesse_id"] = "ref class|CGrossesse";
    $props["date_debut"]   = "dateTime notNull";
    $props["date_fin"]     = "dateTime moreEquals|date_debut";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "Allaitement du " . CMbDT::transform($this->date_debut, null, CAppUI::conf("date")) . " à " . CMbDT::transform($this->date_debut, null, CAppUI::conf("time"));

    if ($this->date_fin) {
      $this->_view .= " au " . CMbDT::transform($this->date_fin, null, CAppUI::conf("date")) . " à " . CMbDT::transform($this->date_fin, null, CAppUI::conf("time"));
    }
  }
}
