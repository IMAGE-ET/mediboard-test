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

  showXds : function(form) {
    var getParameters = form.action.toQueryParams();
    new Url("xds", "ajax_entete_cda_xds")
      .addFormData(form)
      .requestUpdate("enteteXds", {method: "post", getParameters :  getParameters});
    return false;
  },

  action : function(action) {
    new Url("xds", "vw_tools_xds")
      .addParam("action", action)
      .requestUpdate("resultAction");
  }

};