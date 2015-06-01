/**
 * JS function mediuser
 *
 * @category mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CMediusers = {
  editMediuser: function(user_id, element) {
    if (element) {
      element.up('tr').addUniqueClassName('selected');
    }

    new Url("mediusers", "ajax_edit_mediuser")
      .addParam("user_id", user_id)
      .requestModal(800, 700);
  },

  doesMediuserExist: function (adeli) {
    if (!adeli) {
      return false;
    }

    new Url('mediusers', 'ajax_does_mediuser_exist')
      .addParam('adeli', adeli)
      .requestJSON(
      function (id) {
        if (id) {
          CMediusers.editMediuser(id);
        }
        else {
          SystemMessage.notify("<div class='error'>" + $T('CMediusers-doesnt-exist') + "</div>");
        }
      });

    return false;
  }
}