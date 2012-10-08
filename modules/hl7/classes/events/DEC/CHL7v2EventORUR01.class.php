<?php

/**
 * R01 - Observation results reports for the patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventORUR01
 * R01 - Observation results reports for the patient
 */
class CHL7v2EventORUR01 extends CHL7v2EventDEC implements CHL7EventORUR01 {
  var $code = "R01";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($sejour) {
    parent::build($sejour);
    
    /* @todo Pas de création de message pour le moment */
  }
}

?>