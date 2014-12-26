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
      .requestModal("90%");
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
  },

  selectedActionType: function(element, value) {
    element.next('.transformation-'+value).addUniqueClassName('selected');
  },

  fillSelect : function(select, select_name) {
    var selector = select.form[select_name];
    var option   = select.options[select.selectedIndex];

    selector.selectedIndex = -1;
    selector.select('option').invoke('hide');

    var parent = option && option.get("parent");
    var data = parent ? parent+'-'+select.value : select.value;
    selector.select('option[data-parent='+data+']').invoke('show');

    if (selector.getAttribute("onchange")) {
      selector.onchange();
    }
  },

  showVersions : function(transformation_rule_id, standard_name, profil_name) {
    new Url("eai", "ajax_show_transformation_rule_versions")
      .addParam("transformation_rule_id", transformation_rule_id)
      .addParam("standard_name", standard_name)
      .addParam("profil_name", profil_name)
      .requestUpdate("EAITransformationRule-version");
  }
}