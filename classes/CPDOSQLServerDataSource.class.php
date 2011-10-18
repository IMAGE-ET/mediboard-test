<?php /* $Id: mysqlDataSource.class.php 12478 2011-06-20 08:10:44Z lryo $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12478 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * MySQL vs. SQLServer
 * http://technet.microsoft.com/en-us/library/cc966396.aspx
 * http://www.codeproject.com/KB/database/migrate-mysql-to-mssql.aspx
 */

class CPDOSQLServerDataSource extends CPDODataSource {
  protected $driver_name = "sqlserv";
}
