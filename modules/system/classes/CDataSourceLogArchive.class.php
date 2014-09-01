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
 * Data source resource usage log
 */
class CDataSourceLogArchive extends CDataSourceLog {
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = 'datasource_log_archive';

    return $spec;
  }
}
