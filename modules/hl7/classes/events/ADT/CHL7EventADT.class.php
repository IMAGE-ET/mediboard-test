<?php

/**
 * Admit Discharge Transfer HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventADT 
 * Admit Discharge Transfer
 */
interface CHL7EventADT {
  function __construct();
  
  function build($object);
  
  function buildI18nSegments($object);
}

?>