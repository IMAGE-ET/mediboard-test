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
require_once( "Task.php" );

class Procedure extends Task {
  
  function Procedure() {
  }
  
  function createQuestion( $question, $default = null ) {
    return new Question( $question, $default );
  }
  
  function askQuestion( $question ) {
    if ( $question instanceOf Question ) {
      return recup( $question->qt, $question->def );
    }
  }
}

?>