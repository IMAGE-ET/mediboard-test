/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Category = {
  loadList : function() {
    var url = new Url("files", "ajax_list_categories");
    url.requestUpdate('list_file_category');
  },

  edit : function(category_id) {
    var url = new Url("files", "ajax_edit_category");
    url.addParam("category_id", category_id);
    url.requestUpdate('edit_file_category');
  },

  callback : function(id) {
    Category.loadList();
    Category.edit(id);
  }
};