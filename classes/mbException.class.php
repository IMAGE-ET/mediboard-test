<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 10311 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbException extends Exception {  
  public function __construct($text) {
    $args = func_get_args();
    $text = CAppUI::tr($text, array_slice($args, 1));
        
    parent::__construct($text, 0); 
  } 
}



?>