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

DrawingItem = {
  editModal : function(_id, src_file_id) {
    var url = new Url("drawing", "ajax_draw");
    url.addParam('id', _id);
    url.addParam('src_file_id', src_file_id);
    url.requestModal("1024","680");

    url.modalObject.observe('afterClose', function(a) {
      //cleanup observing
      document.stopObserving("keydown");
      document.stopObserving("contextmenu");
      window.location.reload();
    });
  }
};