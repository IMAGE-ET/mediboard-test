<?php

/**
 * A47 - Change patient identifier list - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA47_FR
 * A47 - Change patient identifier list
 */
class CHL7v2EventADTA47_FR extends CHL7v2EventADTA47 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA47_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
}