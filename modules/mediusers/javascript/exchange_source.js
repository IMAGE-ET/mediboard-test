/**
 * manage the source exchange
 *
 * @category mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

exchangeSources = {
  popModal : function(pop_id) {
    var url = new Url("system", "ajax_edit_sourcePOP");
    url.addParam("source_id", pop_id);
    url.requestModal();
  },

  refreshListPOP : function() {
    var url = new Url("system", "ajax_list_sourcePOP");
    url.requestUpdate("list_sourcePOP");
  }

}