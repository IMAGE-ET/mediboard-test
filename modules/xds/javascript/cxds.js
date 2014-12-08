/**
 * $Id$
 *
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Cxds = {

  action : function(action) {
    new Url("xds", "vw_tools_xds")
      .addParam("action", action)
      .requestUpdate("resultAction");
  }

};