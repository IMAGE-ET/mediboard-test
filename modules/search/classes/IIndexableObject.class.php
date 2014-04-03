<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Search Related interface, can be used on any class linked to a search
 */
interface IIndexableObject {

  /**
   * Get the patient_id of CMbobject
   *
   * @return string
   */
  function getFieldPatient ();
  /**
   * Loads the related fields for indexing datum (patient_id et date)
   *
   * @return array
   */
  function getFieldsSearch ();

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function redesignBody ($content);
}

