window.Facture = {
  modal: null,
  load: function(oForm, patient_id, praticien_id, consult_id, not_load_banque) {
    var url = new Url('dPcabinet' , 'ajax_add_consult_facture');
    url.addParam('patient_id'     , patient_id);
    url.addParam('executant_id'   , praticien_id);
    url.addParam('consult_id'     , consult_id);
    url.addParam('type_facture'   , oForm.type_facture.value);
    url.addParam('not_load_banque', not_load_banque);
    url.requestUpdate('load_facture');
  },
  reload: function(patient_id, consult_id, not_load_banque, facture_id) {
    var url = new Url('dPcabinet' , 'ajax_view_facture');
    url.addParam('patient_id'     , patient_id);
    url.addParam('consult_id'     , consult_id);
    url.addParam('not_load_banque', not_load_banque);
    url.addParam('facture_id'     , facture_id);
    url.requestUpdate('load_facture');
  },
  modifCloture: function(oForm) {
    onSubmitFormAjax(oForm, {
      onComplete : function() {
      var url = new Url('dPcabinet'   , 'ajax_view_facture');
        url.addParam('facture_id'     , oForm.facture_id.value);
        url.addParam('not_load_banque', oForm.not_load_banque.value);
        url.requestUpdate('load_facture');
    }
    });
  },
  reloadReglement: function(facture_id) {
    var url = new Url('dPcabinet', 'ajax_refresh_reglement');
    url.addParam('facture_id'    , facture_id);
    url.requestUpdate('reglements_facture');
  },
  cut: function(oForm) {
    onSubmitFormAjax(oForm, {
      onComplete : function() {
        var url = new Url('dPcabinet', 'ajax_view_facture');
        url.addParam('facture_id'    , oForm.facture_id.value);
        url.requestUpdate("load_facture");
        Facture.modal.close();
      }
    });
  }
};