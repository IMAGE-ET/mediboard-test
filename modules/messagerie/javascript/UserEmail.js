/**
 * JS function UserEmail
 *
 * @category messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

messagerie = {
  module : "messagerie",

  modalPOPOpen: function(id) {
    var url = new Url(messagerie.module, "ajax_open_pop_email");
    url.addParam("mail_id"    , id);
    url.requestModal();
  },

  refresh : function() {}

}