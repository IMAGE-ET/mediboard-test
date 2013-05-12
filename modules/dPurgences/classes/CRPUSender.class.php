<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

abstract class CRPUSender {
  function loadExtractPassages(CExtractPassages &$extractPassages) {}

  function extract(CExtractPassages $extractPassages, $rpus) {}
    
  function encrypt(CExtractPassages $extractPassages) {}
  
  function transmit(CExtractPassages $extractPassages) {}
}
