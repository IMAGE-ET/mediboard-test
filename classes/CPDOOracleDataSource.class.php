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
 * Class CPDOOracleDataSource
 */
class CPDOOracleDataSource extends CPDODataSource {
  protected $driver_name = "oci";

  /**
   * Get the used grammar
   *
   * @return CSQLGrammarOracle|mixed
   */
  function getQueryGrammar() {
    return new CSQLGrammarOracle();
  }
}
