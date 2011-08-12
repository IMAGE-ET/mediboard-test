<?php

/**
 * Operator IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("eai", "CEAIOperator");

/**
 * Class COperatorIHE 
 * Operator IHE
 */
class COperatorIHE extends CEAIOperator {
  function event(CExchangeDataFormat $data_format) {
    $msg = $data_format->_message;
   
    
    return null;
  }
}

?>