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

  editActe: function(acte_id, sejour_guid, oOptions) {
    var oDefaultOptions = {
      onClose: function() {PMSI.reloadActesCCAM(sejour_guid);}
    };
    Object.extend(oDefaultOptions, oOptions);
    var url = new Url("salleOp", "ajax_edit_acte_ccam");
    url.addParam("acte_id", acte_id);
    url.requestModal(null, null, oDefaultOptions);
    window.urlEditActe = url;
  },

  submitFunction : function (form) {
  new Url("dPccam", "ajax_function_keyword")
      .addFormData(form)
      .requestUpdate("result_keyword");

  return false;
  },

  remiseAZeroSelect : function (select) {
    var form = select.form;
    if (select.name == "chap1") {
      form.elements.result_chap2.value = "";
      form.elements.result_chap3.value = "";
      form.elements.result_chap4.value = "";
      form.elements.chap2.update();
      form.elements.chap3.update();
      form.elements.chap4.update();
    }
    else if (select.name == "chap2") {
      form.elements.result_chap3.value = "";
      form.elements.result_chap4.value = "";
      form.elements.chap3.update();
      form.elements.chap4.update();
    }
    else if (select.name == "chap3") {
      form.elements.result_chap4.value = "";
      form.elements.chap4.update();
    }
  },

  associeFonction : function (select) {
    var value = $V(select);
    var form = select.form;
    if (value == "Choisir le 1er niveau du chapitre") {
      form.elements.result_chap1.value = "";
    }
    else if (value == "Choisir le niveau suivant") {
      switch (select.name) {
        case 'chap2' :
          form.elements.result_chap2.value = "";
        case 'chap3' :
          form.elements.result_chap3.value = "";
        case 'chap4' :
          form.elements.result_chap4.value = "";
      }
    }
    else {
      var form = select.form;
      $V(form.elements["result_" + select.name], value);
      var number_last_letter = parseInt(select.get("index")) + 1;
      var next_ID = select.name.substr(0, select.name.length - 1) + number_last_letter;
      var data = select.options[select.selectedIndex].get('code-pere');

      new Url("dPccam", "ajax_refresh_select")
          .addParam("value_selected", value)
          .addParam("codePere", data)
          .requestUpdate(select.form.elements[next_ID]);
    }
  },

  cacheElements : function () {
    $("keywords").setAttribute("disabled", true);
    $("chap1").setAttribute("disabled", true);
    $("chap2").setAttribute("disabled", true);
    $("chap3").setAttribute("disabled", true);
    $("chap4").setAttribute("disabled", true);
  },

  refreshCodeFrom : function (code_ccam, select) {
    new Url("dPccam", "ajax_show_code")
        .addParam("code_ccam"   , code_ccam)
        .addParam("date_version", $V(select))
        .requestUpdate("info_code");
  },

  show_code : function (code_ccam, date_demandee) {
    new Url("dPccam", "ajax_show_code")
        .addParam("code_ccam", code_ccam)
        .addParam("date_demandee", date_demandee)
        .requestModal(900, 800, {});
  },

  refreshModal : function (code_ccam) {
    Control.Modal.close();
    new Url("dPccam", "ajax_show_code")
        .addParam("code_ccam", code_ccam)
        .requestModal(900, 800, {});
  }
};