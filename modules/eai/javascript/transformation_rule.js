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
  modal          : null,
  standards_flat : [],
  selects        : ["standard", "domain", "profil", "transaction", "message"],

  edit: function(transformation_rule_id, transformation_ruleset_id, mode_duplication) {
    new Url("eai", "ajax_edit_transformation_rule")
      .addParam("transformation_rule_id", transformation_rule_id)
      .addParam("transformation_ruleset_id", transformation_ruleset_id)
      .addParam("mode_duplication", mode_duplication)
      .requestModal("90%", "75%");
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

  fillSelect: function(select_name, traduction, value, create) {
    var select = $("EAITransformationRule-"+select_name);
    select.update();

    if (!create) {
      value = null;
    }

    $A(EAITransformationRule.standards_flat).pluck(select_name).uniq().each(function(pair){
      if (pair == "none") {
        return;
      }

      var option_text = traduction ? $T(pair+traduction)+' ('+pair+')' : $T(pair);
      select.insert(
        DOM.option({
          value:   pair,
          onclick: "EAITransformationRule.showFillSelect(this.up())",
          selected: value == pair
        }).update(option_text)
      );
    });
  },

  showFillSelect : function(select) {
    var select_name  = select.name;

    var selects = select.form.select("select.EAITransformationRule-select[name != "+select_name+"][name != standard]");

    EAITransformationRule.selects.each(function(selectname) {
      if (selectname == select_name) {
        return;
      }

      var other_select = select.form[selectname];

      var old_selected_value = "";
      if (other_select.selectedIndex != -1) {
        old_selected_value = $V(other_select);
      }

      var filtered = EAITransformationRule.standards_flat.filter(EAITransformationRule.isValueExist.curry(select, selects)).pluck(other_select.name).uniq();

      if (filtered.length == 0) {
        EAITransformationRule.fillSelect(selectname);

        return;
      }

      other_select.update();

      filtered.each(function(option) {
        var option_text = $T(option);
        if (selectname == "domain" || selectname == "profil") {
          option_text = $T(option+'-desc')+' ('+option+')'
        }

        if (option == "none") {
          return;
        }

        other_select.insert(
          DOM.option({
              value: option,
              onclick: "EAITransformationRule.showFillSelect(this.up())"}
          ).update(option_text)
        );
      });

      if (old_selected_value) {
        $V(other_select, old_selected_value);
      }
    });
  },

  isValueExist : function(select, selects, element) {
    var select_value = $V(select);
    var select_name  = select.name;

    var flag = true;
    selects.each(function(other_select) {
      var value_select = $V(other_select); /*|| (other_select.options.length == 1 ? other_select.options[0].value : "");*/

      if (value_select && element[other_select.name] != value_select) {
        flag = false;
      }
    });

    return (element[select_name] == select_value) && flag;
  },

  showVersions : function(transformation_rule_id, standard_name, profil_name) {
    new Url("eai", "ajax_show_transformation_rule_versions")
      .addParam("transformation_rule_id", transformation_rule_id)
      .addParam("standard_name", standard_name)
      .addParam("profil_name", profil_name)
      .requestUpdate("EAITransformationRule-version");
  },

  target: function(form, target) {
    EAITransformationRule.modal = new Url("eai", "ajax_target_transformation_rule")
      .addFormData(form)
      .addParam("target", target)
      .requestModal("90%");
  },

  refreshTarget: function(components) {
    var container = $("EAITransformationRule-component_from");

    components.split("|").each(function(value) {
      container.insert(DOM.span({className:'circled'}, value));
    });

    $V(getForm("editEAITransformationRule").component_from, $A(container.select("span")).pluck("innerHTML").join("|"));

    Control.Modal.close();
  }
}