<?php

/**  
 * @category cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

require_once( "utils.php" );

class Question extends Task {
  
  public $qt    = null;
  public $def   = null;
  
  function Question( $qt, $def = null ) {
    $this->qt   = $qt;
    $this->def  = $def;
  }
}

?>