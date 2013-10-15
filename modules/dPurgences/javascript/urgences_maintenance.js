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

urgencesMaintenance = {

  displaySejour : function (form) {
    new Url("urgences", "ajax_doublon_rpu")
      .addFormData(form)
      .requestUpdate("display_sejour");

    return false;
  },

  checkRPU : function () {
    new Url("urgences", "ajax_check_rpu")
      .requestModal(1024, 768);
  }
};