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
  editModal : function(_id, mode, mode_id, afterClose) {
    var url = new Url('drawing', 'ajax_edit_drawing_category');
    url.addParam("id", _id);
    if (mode && mode_id) {
      url.addParam('mode', mode+"_id");
      url.addParam('mode_id', mode_id);
    }
    url.requestModal();
    url.modalObject.observe('afterClose', function() {
      if (afterClose) {
        afterClose();
      }
    });
  }
};