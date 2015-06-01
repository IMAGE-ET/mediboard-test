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

CFunctions = {
  editFunction: function(function_id, element) {
    if (element) {
      element.up('tr').addUniqueClassName('selected');
    }

    new Url("mediusers", "ajax_edit_function")
      .addParam("function_id", function_id)
      .requestModal(800, 600)
      .modalObject.observe("afterClose", function() {
        getForm('listFilter').onsubmit();
      });
  },

  changePage: function(page) {
    $V(getForm('listFilter').page, page);
  },

  changeFilter : function(order, way) {
    var form = getForm('listFilter');
    $V(form.order_col, order);
    $V(form.order_way, way);

    form.onsubmit();
  }
}