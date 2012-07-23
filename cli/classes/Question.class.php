<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once dirname(__FILE__)."/../utils.php";

/**
 * Question: Enable you to create Questions for a Task
 */
class Question extends Task {

  public $qt    = null;
  public $def   = null;
  
  /**
   * Constructor
   * 
   * @param string $qt  The question to ask for
   * @param string $def Default value
   * 
   * @return
   */
  function Question( $qt, $def = null ) {
    $this->qt   = $qt;
    $this->def  = $def;
  }
}

?>