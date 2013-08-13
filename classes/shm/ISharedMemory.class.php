<?php 

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Shared Memory interface
 */
interface ISharedMemory {

  /**
   * Initialize the shared memory
   * Returns true if shared memory is available
   *
   * @return bool
   */
  function init();

  /**
   * Get a variable from shared memory
   *
   * @param string $key Key of value to retrieve
   *
   * @return mixed the value, null if failed
   */
  function get($key);

  /**
   * Put a variable into shared memory
   *
   * @param string $key   Key of value to store
   * @param mixed  $value The value
   *
   * @return bool job-done
   */
  function put($key, $value);

  /**
   * Remove a variable from shared memory
   *
   * @param string $key Key of value to remove
   *
   * @return bool job-done
   */
  function rem($key);

  /**
   * Clears the shared memory
   *
   * @return bool job-done
   */
  //function clear();

  /**
   * Return the list of keys
   *
   * @param string $prefix The keys' prefix
   *
   * @return array Keys list
   */
  function listKeys($prefix);

  /**
   * Get modification date
   *
   * @param string $key Key
   *
   * @return string ISO date
   */
  function modDate($key);
}