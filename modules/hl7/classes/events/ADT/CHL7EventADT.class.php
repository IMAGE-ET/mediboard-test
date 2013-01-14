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
  /**
   * Construct
   *
   * @return \CHL7EventADT
   */
  function __construct();

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object);

  /**
   * Build i18n segements
   *
   * @param CMbObject $object Object
   *
   * @see parent::buildI18nSegments()
   *
   * @return void
   */
  function buildI18nSegments($object);
}

?>