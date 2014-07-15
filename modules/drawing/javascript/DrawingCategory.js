/**
 * $Id$
 *
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

DrawingCategory = {
  editModal : function(_id, afterClose) {
    var url = new Url('drawing', 'ajax_edit_drawing_category');
    url.addParam("object_id", _id);
    url.requestModal();
    url.modalObject.observe('afterClose', function() {
      if (afterClose) {
        afterClose();
      }
    });
  }
};