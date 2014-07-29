/**
 * $Id$
 *
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Allaitement = {
  viewAllaitements: function(patient_id) {
    var url = new Url("maternite", "ajax_bind_allaitement");
    url.addParam("patient_id", patient_id);
    url.requestModal(900, 400, {onClose: function() {
      Grossesse.updateGrossesseArea();
      Grossesse.updateEtatActuel();
    }});
  },

  editAllaitement: function(allaitement_id, patient_id) {
    var url = new Url("maternite", "ajax_edit_allaitement");
    url.addParam("allaitement_id", allaitement_id);
    url.addNotNullParam("patient_id", patient_id);
    url.requestUpdate("edit_allaitement");
  },

  refreshList: function(patient_id, object_guid) {
    var url = new Url("maternite", "ajax_list_allaitements");
    url.addNotNullParam("patient_id", patient_id);
    url.addParam("object_guid", object_guid);
    url.requestUpdate("list_allaitements");
  },

  afterEditAllaitement: function(allaitement_id) {
    Allaitement.editAllaitement(allaitement_id);
    Allaitement.refreshList();
  }
};