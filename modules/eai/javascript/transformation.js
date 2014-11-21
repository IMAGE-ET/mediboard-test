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
  edit: function(transformation_id, event_name, actor_guid) {
    new Url("eai", "ajax_edit_transformation")
      .addParam("transformation_id", transformation_id)
      .addParam("event_name"       , event_name)
      .addParam("actor_guid"       , actor_guid)
      .requestModal(600);
  },

  link: function(event_name, actor_guid) {
    new Url("eai", "ajax_link_transformation_rules")
      .addParam("event_name", event_name)
      .addParam("actor_guid", actor_guid)
      .requestModal(600);
  },

  list: function(transformation_id, event_name, actor_guid) {
    new Url("eai", "ajax_list_transformations")
      .addParam("transformation_id", transformation_id)
      .addParam("event_name"       , event_name)
      .addParam("actor_guid"       , actor_guid)
      .requestModal(600);
  }
}