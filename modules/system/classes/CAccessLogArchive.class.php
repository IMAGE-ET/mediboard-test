<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Access Log
 */
class CAccessLogArchive extends CAccessLog {
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = 'access_log_archive';

    return $spec;
  }
}
