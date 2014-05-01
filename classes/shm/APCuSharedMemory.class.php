<?php 

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

require_once __DIR__."/APCSharedMemory.class.php";

/**
 * Alternative PHP User Cache (APCu) based Memory class
 */
class APCuSharedMemory extends APCSharedMemory {
  protected $_cache_key = "key";
}