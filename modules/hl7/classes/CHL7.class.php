<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
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
