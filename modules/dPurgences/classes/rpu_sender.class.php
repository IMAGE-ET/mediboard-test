<?php
/**
 *  @package Mediboard
 *  @subpackage dPurgences
 *  @version $Revision: 6345 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

abstract class CRPUSender {
  function extract(CExtractPassages $extractPassages, $rpus) {}
    
  function encrypt(CExtractPassages $extractPassages) {}
  
  function transmit(CExtractPassages $extractPassages) {}
}
?>