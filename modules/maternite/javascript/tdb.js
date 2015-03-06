/**
 * $Id$
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Tdb = {
  searchGrossesse : function() {
    var url = new Url('maternite', 'ajax_modal_search_grossesse');
    url.addParam("lastname", $V('_seek_patient'));
    url.requestModal("1000", "600");
  },

  editRdvConsult : function(_id, grossesse_id, patient_id) {
    var url = new Url('dPcabinet', 'edit_planning');
    url.addParam('consultation_id', _id);
    url.addParam('grossesse_id', grossesse_id);
    url.addParam('pat_id', patient_id);
    url.addParam("dialog", 1);
    url.addParam("modal", 1);
    url.addParam("callback", 'afterEditConsultMater');
    url.requestModal("900", "600");
  },


  editConsult : function(consultation_id) {
    var url = new Url("dPcabinet", "ajax_full_consult");
    url.addParam("consult_id", consultation_id);
    url.modal({
      width: "95%",
      height: "95%"
    });

  },

  editGrossesse : function(_id, patient_id) {
    var url = new Url('maternite', 'ajax_edit_grossesse', "action");
    url.addParam('grossesse_id', _id);
    url.addParam('parturiente_id', patient_id);
    url.addParam('with_buttons', 1);
    url.requestModal(800, 350);
    url.modalObject.observe('afterClose', function() {
      Tdb.views.listGrossesses();
    });
  },

  editSejour : function(_id, grossesse_id, patiente_id) {
    var url = new Url('dPplanningOp', 'vw_edit_sejour');
    url.addParam('sejour_id', _id);
    url.addParam('grossesse_id', grossesse_id);
    url.addParam('patient_id', patiente_id);
    url.addParam("dialog", 1);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: function() {
        Tdb.views.listGrossesses();
      }
    });
  },

  editD2S : function(sejour_id, op_id) {
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("operation_id", op_id);
    url.addParam("modal", 0);
    url.modal({width: "95%", height: "95%"});
  },

  dossierAccouchement : function(op_id) {
    var url = new Url("salleOp", "ajax_vw_operation");
    url.addParam("operation_id", op_id);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: Tdb.views.listAccouchements
    });
  },

  editAccouchement : function(_id, sejour_id, grossesse_id, patiente_id, callback) {
    var url = new Url('dPplanningOp', 'vw_edit_urgence');
    url.addParam("operation_id", _id);
    url.addParam("sejour_id", sejour_id);
    url.addParam("grossesse_id", grossesse_id);
    url.addParam("pat_id", patiente_id);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: function() {
        if (callback) {
          callback();
        }
        Tdb.views.listGrossesses(true);
      }
    });
  },

  changeSalleFor : function(op_id, salle_id) {
    var form = getForm('changeSalleForOp');
    $V(form.operation_id, op_id);
    $V(form.salle_id, salle_id);
    form.onsubmit();
  },

  changeAnesthFor : function(op_id, anesth_id) {
    var form = getForm('changeAnesthForOp');
    $V(form.operation_id, op_id);
    $V(form.anesth_id, anesth_id);
    form.onsubmit();
  },

  changeStatusConsult : function(consult_id, status) {
    var form = getForm('changeStatusConsult');
    $V(form.consultation_id, consult_id);
    $V(form.chrono, status);
    form.onsubmit();
  },

  views : {
    see_finished : 1,
    date : '',

    toggleFinished : function() {
      Tdb.views.see_finished = +!Tdb.views.see_finished;
      Tdb.views.listAccouchements();
    },

    initListGrossesses : function() {
      var url = new Url("maternite", "ajax_tdb_grossesses");
      url.addParam("date", Tdb.views.date);
      url.periodicalUpdate("grossesses", { frequency: 120, onSuccess: function() {
        Tdb.views.listConsultations(true);
        }
      });
    },

    listGrossesses : function(cascade) {
      var url = new Url("maternite", "ajax_tdb_grossesses");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("grossesses", {onSuccess: function() {
        if (cascade) {
          Tdb.views.listConsultations(cascade);
        }
      }
      });
    },

    listConsultations : function(cascade) {
      var url = new Url("maternite", "ajax_tdb_consultations");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("consultations", { onSuccess: function() {
          if (cascade) {
            Tdb.views.listHospitalisations(cascade);
          }
      }
      });
    },

    listHospitalisations : function(cascade) {
      var url = new Url("maternite", "ajax_tdb_hospitalisations");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("hospitalisations", {
        onSuccess: function () {
          if (cascade) {
            Tdb.views.listAccouchements(cascade);
          }
        }
      });
    },

    listAccouchements : function(cascade) {
      var url = new Url("maternite", "ajax_tdb_accouchements");
      url.addParam("date", Tdb.views.date);
      url.addParam("see_finished", Tdb.views.see_finished);
      url.requestUpdate("accouchements");
    },

    filterByText : function(target) {
      var input = $('_seek_patient');
      var value = $V(input);

      var tables = ['grossesses_tab', 'consultations_tab', 'hospitalisation_tab', 'accouchements_tab'];
      if (target) {
        tables = [target];
      }
      for (var a = 0; a < tables.length; a++) {
        var key = tables[a];
        var elt = key == 'hospitalisation_tab' ? "tbody" : "tr";
        if (!value) {
          $(key).select(".CPatient-view").each(function(e) {
            e.up(elt).show();
          });
        }
        else {
          $(key).select(".CPatient-view").each(function (e) {
            if (!e.innerHTML.like(value)) {
              e.up(elt).hide();
            }
            else {
              e.up(elt).show();
            }
          });
        }
      }
    }
  }
};