/**
 * Transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

EAITransformationRule = {
  edit: function(transformation_rule_id, transformation_ruleset_id, mode_duplication) {
    new Url("eai", "ajax_edit_transformation_rule")
      .addParam("transformation_rule_id", transformation_rule_id)
      .addParam("transformation_ruleset_id", transformation_ruleset_id)
      .addParam("mode_duplication", mode_duplication)
      .requestModal(600);
  },

  stats: function(transformation_rule_id) {
    new Url("eai", "ajax_show_stats_transformations")
      .addParam("transformation_rule_id", transformation_rule_id)
      .requestModal(600);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, Control.Modal.close);
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
  }
}