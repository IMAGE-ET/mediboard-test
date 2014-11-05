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
  editConsult : function(_id, patient_id) {
    var url = new Url('dPcabinet', 'edit_planning');
    url.addParam('consultation_id', _id);
    url.addParam('pat_id', patient_id);
    url.requestModal();
  },

  editGrossesse : function(_id, patient_id) {
    var url = new Url('maternite', 'ajax_edit_grossesse', "action");
    url.addParam('grossesse_id', _id);
    url.addParam('parturiente_id', patient_id);
    url.requestModal();
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

  editAccouchement : function(_id, sejour_id, grossesse_id, patiente_id) {
    var url = new Url('dPplanningOp', 'vw_edit_urgence');
    url.addParam("operation_id", _id);
    url.addParam("sejour_id", sejour_id);
    url.addParam("grossesse_id", grossesse_id);
    url.addParam("pat_id", patiente_id);
    url.requestModal("-40","-40");
  },

  views : {
    date : '',
    initListGrossesses : function() {
      var url = new Url("maternite", "ajax_tdb_grossesses");
      url.addParam("date", Tdb.views.date);
      url.periodicalUpdate("grossesses", { frequency: 10, onSuccess: Tdb.views.listConsultations } );
    },

    listGrossesses : function() {
      var url = new Url("maternite", "ajax_tdb_grossesses");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("grossesses", {onSuccess: Tdb.views.listConsultations } );
    },

    listConsultations : function() {
      var url = new Url("maternite", "ajax_tdb_consultations");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("consultations", { onSuccess: Tdb.views.listHospitalisations } );
    },

    listHospitalisations : function() {
      var url = new Url("maternite", "ajax_tdb_hospitalisations");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("hospitalisations", { onSuccess: Tdb.views.listAccouchements } );
    },

    listAccouchements : function() {
      var url = new Url("maternite", "ajax_tdb_accouchements");
      url.addParam("date", Tdb.views.date);
      url.requestUpdate("accouchements", { onSuccess: Tdb.views.filterByText} );
    },

    filterByText : function() {
      var input = $('_seek_patient');
      var value = $V(input);

      var tables = ['grossesses_tab', 'consultations_tab', 'hospitalisation_tab', 'accouchements_tab'];
      for (var a = 0; a < tables.length; a++) {
        if (!value) {
          $(tables[a]).select(".CPatient-view").each(function(e) {
            e.up("tr").show();
          });
        }
        else {
          $(tables[a]).select(".CPatient-view").each(function (e) {
            if (!e.innerHTML.like(value)) {
              e.up("tr").hide();
            }
          });
        }
      }
    }
  }
};