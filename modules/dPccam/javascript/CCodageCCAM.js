/**
 * JS function Codage CCAM
 *
 * @category dPccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCodageCCAM = {
  changeCodageMode: function(element, codage_id) {
    var codageForm = getForm("formCodageRules_codage-" + codage_id);
    if($V(element)) {
      $V(codageForm.association_mode, "user_choice");
    }
    else {
      $V(codageForm.association_mode, "auto");
    }
    codageForm.onsubmit();
  },

  syncCodageField: function(element, view) {
    var acteForm = getForm('codageActe-' + view);
    var fieldName = element.name;
    var fieldValue = $V(element);
    $V(acteForm[fieldName], fieldValue);
    if($V(acteForm.acte_id)) {
      acteForm.onsubmit();
    }
    else {
      CCodageCCAM.checkModificateurs(element, view);
    }
  },

  checkModificateurs: function(input, acte) {
    var exclusive_modifiers = ['F', 'P', 'S', 'U'];
    var checkboxes = $$('input[data-acte="' + acte + '"].modificateur');
    var nb_checked = 0;
    var exclusive_modifier = '';
    var exclusive_modifier_checked = false;
    checkboxes.each(function(checkbox) {
      if (checkbox.checked) {
        nb_checked++;
        if (checkbox.get('double') == 2) {
          nb_checked++;
        }
        if (exclusive_modifiers.indexOf(checkbox.get('code')) != -1) {
          exclusive_modifier = checkbox.get('code');
          exclusive_modifier_checked = true;
        }
      }
    });

    checkboxes.each(function(checkbox) {
      checkbox.disabled = (!checkbox.checked && nb_checked == 4) ||
        (exclusive_modifiers.indexOf(exclusive_modifier) != -1 && exclusive_modifiers.indexOf(checkbox.get('code')) != -1 && !checkbox.checked && exclusive_modifier_checked);
    });

    var container = input.up();
    if (input.checked && container.hasClassName('warning')) {
      container.removeClassName('warning');
      container.addClassName('error');
    }
    else if (!input.checked && container.hasClassName('error')) {
      container.removeClassName('error');
      container.addClassName('warning');
    }
  },

  setRule: function(element, codage_id) {
    var codageForm = getForm("formCodageRules_codage-" + codage_id);
    $V(codageForm.association_mode, "user_choice", false);
    var inputs = document.getElementsByName("association_rule");
    for(var i = 0; i < inputs.length; i++) {
      inputs[i].disabled = false;
    }
    $V(codageForm.association_rule, $V(element), false);
    codageForm.onsubmit();
  },

  switchViewActivite: function(value, activite) {
    if(value) {
      $$('.activite-'+activite).each(function(oElement) {oElement.show()});
    }
    else {
      $$('.activite-'+activite).each(function(oElement) {oElement.hide()});
    }
  },

  editActe: function(acte_id, sejour_id, oOptions) {
    var oDefaultOptions = {
      onClose: function() {PMSI.loadActes(sejour_id);}
    };
    Object.extend(oDefaultOptions, oOptions);
    var url = new Url("salleOp", "ajax_edit_acte_ccam");
    url.addParam("acte_id", acte_id);
    url.requestModal(null, null, oDefaultOptions);
    window.urlEditActe = url;
  }
};