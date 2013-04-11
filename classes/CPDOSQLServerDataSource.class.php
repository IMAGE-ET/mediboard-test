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
 * MySQL vs. SQLServer
 * http://technet.microsoft.com/en-us/library/cc966396.aspx
 * http://www.codeproject.com/KB/database/migrate-mysql-to-mssql.aspx
 */

/**
 * Class CPDOSQLServerDataSource
 */
class CPDOSQLServerDataSource extends CPDODataSource {
  protected $driver_name = "sqlserv";

  /**
   * Get the used grammar
   *
   * @return CSQLGrammarSQLServer|mixed
   */
  function getQueryGrammar() {
    return new CSQLGrammarSQLServer();
  }
}
