/**
 * JS function disciplone
 *
 * @category mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CDiscipline = {
  edit: function(discipline_id, element) {
    if (element) {
      element.up('tr').addUniqueClassName('selected');
    }

    new Url("mediusers", "ajax_edit_discipline")
      .addParam("discipline_id", discipline_id)
      .requestModal(800, 600)
      .modalObject.observe("afterClose", function() {
        getForm('listFilter').onsubmit();
      });
  },

  changePage: function(page) {
    $V(getForm('listFilter').page, page);
  }
}