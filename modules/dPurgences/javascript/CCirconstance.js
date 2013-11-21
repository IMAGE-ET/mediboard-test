/**
 * $Id$
 *
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCirconstance = {
  edit : function(id) {
    new Url("dPurgences", "ajax_edit_circonstance")
      .addParam("id", id)
      .requestModal();
  }
};