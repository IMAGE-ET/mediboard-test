window.Facture = {
  modal: null,
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
        url.addElement(oForm.facture_id);
        url.addElement(oForm.not_load_banque);
        url.addParam('object_class'     , oForm.facture_class.value);
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
        url.addElement(oForm.facture_id);
        url.addParam('object_class'     , oForm.facture_class.value);
        url.requestUpdate("load_facture");
        Facture.modal.close();
      }
    });
  }
};