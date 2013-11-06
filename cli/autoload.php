<?php 

/**
 * $Id$
 *  
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Basic Mediboard classes autoloader
 */
spl_autoload_register(
  function ($class) {
    $file = __DIR__."/classes/$class.class.php";

    if (!file_exists($file)) {
      return false;
    }

    include_once $file;

    return true;
  }
);
