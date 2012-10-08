<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

class CCheckable {
  function checkAll($strict = true){
    foreach($this->getAll() as $item) {
      if (!$item->check($strict)) {
        return false;
      }
    }
    
    return true;
  }
}
