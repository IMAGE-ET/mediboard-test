<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Activité CdARR
 */
class CCdARRObject extends CMbObject {
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = 'cdarr';
    $spec->incremented = 0;
    return $spec;
  }
}

?>