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
  modal : null,

  edit: function(transformation_id, message_class, event_class) {
    new Url("eai", "ajax_edit_transformation")
      .addParam("transformation_id", transformation_id)
      .addParam("message_class"    , message_class)
      .addParam("event_class"      , event_class)
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

  link: function(message_class, event_class, actor_guid) {
    new Url("eai", "ajax_link_transformation_rules")
      .addParam("event_class"  , event_class)
      .addParam("message_class", message_class)
      .addParam("actor_guid", actor_guid)
      .requestModal(800);
  },

  list: function(message_class, event_class, actor_guid) {
    EAITransformation.modal = new Url("eai", "ajax_list_transformations")
      .addParam("event_class"  , event_class)
      .addParam("message_class", message_class)
      .addParam("actor_guid"   , actor_guid)
      .requestModal(700);
  },

  refreshList: function(message_class, event_class, actor_guid) {
    new Url("eai", "ajax_refresh_transformations")
      .addParam("message_class", message_class)
      .addParam("event_class"  , event_class)
      .addParam("actor_guid"   , actor_guid)
      .requestUpdate("transformations");
  }
}