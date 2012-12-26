<?php

/**
 * Q22 - Find Candidates - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventPDQQ22
 * Q22 - Find Candidates
 */
class CHL7v2EventPDQQ22 extends CHL7v2EventPDQ implements CHL7EventPDQQ22 {
  var $code = "Q22";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($patient) {
    parent::build($patient);

    // QPD
    $this->addQPD($patient);

    // RCP
    $this->addRCP($patient);
  }
}

?>