/**
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

SYSLOG = {
  test : function(exchange_source_name, action) {
    new Url("system", "ajax_syslog_test")
      .addParam("exchange_source_name", exchange_source_name)
      .addParam("type_action", action)
      .requestUpdate('syslog_test');
  }
};