<?php

/**
 * A44 - Move account information - patient account number - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA44_FR
 * A44 - Move account information - patient account number
 */
class CHL7v2EventADTA44_FR extends CHL7v2EventADTA44 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA44_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
}

?>