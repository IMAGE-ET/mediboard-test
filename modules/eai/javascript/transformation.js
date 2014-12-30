/**
 * Transformation
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

EAITransformation = {
  modal          : null,

  edit: function(transformation_id, event_name) {
    new Url("eai", "ajax_edit_transformation")
      .addParam("transformation_id", transformation_id)
      .addParam("event_name"       , event_name)
      .requestModal(600);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, function() {
      Control.Modal.close();
      EAITransformation.modal.refreshModal().bindAsEventListener(EAITransformation.modal);
    });
  },

  moveRowUp: function(row) {
    if (row.previous() == row.up().childElements()[1]) {
      return;
    }

    row.previous().insert({before: row});
  },

  moveRowDown: function(row) {
    if (row.next()) {
      row.next().insert({after: row});
    }
  },

  link: function(event_name, actor_guid) {
    new Url("eai", "ajax_link_transformation_rules")
      .addParam("event_name", event_name)
      .addParam("actor_guid", actor_guid)
      .requestModal(600);
  },

  list: function(event_name, actor_guid) {
    EAITransformation.modal = new Url("eai", "ajax_list_transformations")
      .addParam("event_name", event_name)
      .addParam("actor_guid", actor_guid)
      .requestModal(600);
  },

  refreshList: function(event_name, actor_guid) {
    new Url("eai", "ajax_refresh_transformations")
      .addParam("event_name", event_name)
      .addParam("actor_guid", actor_guid)
      .requestUpdate("transformations");
  }
}