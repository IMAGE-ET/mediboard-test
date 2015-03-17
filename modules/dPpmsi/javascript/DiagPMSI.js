/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

DiagPMSI = window.DiagPMSI || {

  getAutocompleteCim10PMSI : function(form, input_field, nullify_imput) {
    var oform = form;
    var url = new Url("pmsi", "ajax_seek_cim10_pmsi");
    url.addParam("object_class", "CCIM10");
    url.autoComplete(oform.keywords_code_pmsi, '', {
      minChars:           1,
      dropdown:           true,
      width:              "250px",
      select:             "code",
      afterUpdateElement: function (oHidden) {
        $V(input_field, oHidden.value);
        if (!nullify_imput) {
          $V(form.keywords_code, oHidden.value);
        }
      }
    });
  },

  getAutocompleteCim10 : function (form, input_field, nullify_imput) {
    var element= input_field;
    var url = new Url("cim10", "ajax_code_cim10_autocomplete");
    url.autoComplete(form.keywords_code, '', {
      minChars: 1,
      dropdown: true,
      width: "250px",
      select: "code",
      afterUpdateElement: function(oHidden) {
        $V(element, oHidden.value);
        if (!nullify_imput) {
          $V(form.keywords_code, oHidden.value);
        }
      }
    });
  },

  deleteDiag : function (form, input_field) {
    var oForm = form;
    $V(oForm.keywords_code, "");
    $V(input_field, "");
    oForm.onsubmit();
  },

  input_diag :null,
  initDiagCimPMSI : function (input) {
    this.input_diag = input;
    var url = new Url("pmsi", "vw_cim10_explorer");
    url.addParam("modal", true);
    url.requestModal("95%","95%");
  },

  selectDiag : function (code) {
    $V(this.input_diag, code);
  }

};