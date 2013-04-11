<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CPDOMySQLDataSource
 */
class CPDOMySQLDataSource extends CPDODataSource {
  protected $driver_name = "mysql";

  /**
   * Get the used grammar
   *
   * @return CSQLGrammarMySQL|mixed
   */
  function getQueryGrammar() {
    return new CSQLGrammarMySQL();
  }
}
