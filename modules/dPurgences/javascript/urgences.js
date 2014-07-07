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

Urgences = {
  hospitalize : function(rpu_id) {
    new Url("dPurgences", "ajax_hospitalization_rpu")
      .addParam("rpu_id", rpu_id)
      .requestModal("965", "325");
  },

  checkMerge : function(sejour_id, sejour_id_futur) {
    new Url("dPurgences", "ajax_check_merge")
      .addParam("sejour_id", sejour_id)
      .addParam("sejour_id_futur", sejour_id_futur)
      .requestUpdate("result_merge_"+sejour_id_futur);
  }
};