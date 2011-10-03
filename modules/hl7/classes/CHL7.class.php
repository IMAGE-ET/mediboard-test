<?php

/**
 * HL7 Tools
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7 
 * Tools
 */
class CHL7 {
  static $versions = array ();
}

CHL7::$versions = array (
  "v2" => CHL7v2::$versions,
);

?>