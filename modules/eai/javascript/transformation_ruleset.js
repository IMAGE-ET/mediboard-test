/**
 * $Id$
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

EAITransformationRuleSet = {
  edit: function(transformation_ruleset_id) {
    new Url("eai", "ajax_edit_transformation_ruleset")
      .addParam("transformation_ruleset_id", transformation_ruleset_id)
      .requestModal(600);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, Control.Modal.close);
  },

  onSelect: function(transformation_ruleset_id, selected_tr) {

  },

  refreshList: function() {
    new Url("eai", "ajax_refresh_list_transformation_ruleset")
      .requestUpdate("list-transformation-ruleset");
  }
}