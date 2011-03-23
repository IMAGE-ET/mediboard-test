<?php

/**
 * Dispatcher EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIDispatcher
 * Dispatcher EAI
 */

class CEAIDispatcher {  
  static function dispatch($data) {
    $understand = false;
    //mbTrace($data);
    foreach (CExchangeDataFormat::getAll() as $_data_format) {
      $data_format = new $_data_format;
      $understand = $data_format->understand($data);
      
      if ($understand) {
        return true;
      }
    }
    return false;
  }
}

?>