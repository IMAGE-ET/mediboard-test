<?php

/**
 * A28 - Add person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA28_FR 
 * A28 - Add person information
 */
class CHL7v2EventADTA28_FR extends CHL7v2EventADTA28 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA28_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
}