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
 * Class CHL7v2TableObject 
 * HL7 Table
 */
class CHL7v2TableObject extends CMbObject { 
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = 'hl7v2';
    $spec->incremented = 0;
    return $spec;
  }
}
