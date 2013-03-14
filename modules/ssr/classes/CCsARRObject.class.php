<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Object CsARR
 */
class CCsARRObject extends CStoredObject {
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = 'csarr';
    $spec->incremented = 0;
    return $spec;
  }
}

?>